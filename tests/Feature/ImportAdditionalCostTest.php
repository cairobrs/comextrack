<?php

namespace Tests\Feature;

use App\Models\Import;
use App\Models\ImportCost;
use App\Models\ImportLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportAdditionalCostTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);
    }

    private function regularUser(): User
    {
        return User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    public function test_default_costs_are_created_as_padrao(): void
    {
        $import = Import::factory()->create();

        $this->assertCount(4, $import->costs);
        $this->assertTrue($import->costs->every(fn ($cost) => $cost->isPadrao()));
    }

    public function test_admin_can_create_additional_cost(): void
    {
        $admin = $this->adminUser();
        $import = Import::factory()->create();

        $this->actingAs($admin)->post(route('imports.costs.store', $import), [
            'nome' => 'Taxa de armazenagem extra',
            'valor' => '250.00',
            'moeda' => 'USD',
            'observacoes' => 'Cobrança complementar',
        ])->assertRedirect(route('imports.show', $import));

        $cost = Import::find($import->id)->costs()->where('categoria', ImportCost::CATEGORIA_ADICIONAL)->first();
        $this->assertNotNull($cost);
        $this->assertSame('Taxa de armazenagem extra', $cost->nome);
        $this->assertSame('pendente', $cost->status_pagamento);

        $this->assertTrue(
            ImportLog::where('import_id', $import->id)
                ->where('tipo_evento', 'custo_criado')
                ->exists()
        );
    }

    public function test_non_admin_cannot_create_additional_cost(): void
    {
        $user = $this->regularUser();
        $import = Import::factory()->create();

        $this->actingAs($user)->post(route('imports.costs.store', $import), [
            'nome' => 'Despesa não permitida',
        ])->assertForbidden();
    }

    public function test_non_admin_cannot_update_standard_cost(): void
    {
        $user = $this->regularUser();
        $import = Import::factory()->create();
        $cost = $import->costs()->where('tipo_custo', 'frete_internacional')->first();

        $this->actingAs($user)->put(route('costs.update', $cost), [
            'valor' => '100.00',
            'moeda' => 'USD',
            'status_pagamento' => 'pago',
        ])->assertForbidden();
    }

    public function test_non_admin_cannot_update_additional_cost(): void
    {
        $user = $this->regularUser();
        $import = Import::factory()->create();

        $cost = $import->costs()->create([
            'categoria' => ImportCost::CATEGORIA_ADICIONAL,
            'tipo_custo' => ImportCost::TIPO_ADICIONAL,
            'nome' => 'Despesa existente',
            'moeda' => 'USD',
            'status_pagamento' => 'pendente',
        ]);

        $this->actingAs($user)->put(route('costs.update', $cost), [
            'nome' => 'Tentativa de alteração',
            'moeda' => 'USD',
            'status_pagamento' => 'pago',
        ])->assertForbidden();
    }

    public function test_non_admin_cannot_delete_additional_cost(): void
    {
        $user = $this->regularUser();
        $import = Import::factory()->create();

        $cost = $import->costs()->create([
            'categoria' => ImportCost::CATEGORIA_ADICIONAL,
            'tipo_custo' => ImportCost::TIPO_ADICIONAL,
            'nome' => 'Despesa protegida',
            'moeda' => 'USD',
            'status_pagamento' => 'pendente',
        ]);

        $this->actingAs($user)->delete(route('costs.destroy', $cost))->assertForbidden();
    }

    public function test_admin_can_delete_additional_cost(): void
    {
        $admin = $this->adminUser();
        $import = Import::factory()->create();

        $cost = $import->costs()->create([
            'categoria' => ImportCost::CATEGORIA_ADICIONAL,
            'tipo_custo' => ImportCost::TIPO_ADICIONAL,
            'nome' => 'Despesa temporária',
            'moeda' => 'USD',
            'status_pagamento' => 'pendente',
        ]);

        $this->actingAs($admin)->delete(route('costs.destroy', $cost))
            ->assertRedirect(route('imports.show', $import));

        $this->assertDatabaseMissing('import_costs', ['id' => $cost->id]);
        $this->assertTrue(
            ImportLog::where('import_id', $import->id)
                ->where('tipo_evento', 'custo_excluido')
                ->exists()
        );
    }

    public function test_cannot_delete_standard_cost(): void
    {
        $admin = $this->adminUser();
        $import = Import::factory()->create();
        $cost = $import->costs()->where('tipo_custo', 'frete_internacional')->first();

        $this->actingAs($admin)->delete(route('costs.destroy', $cost))->assertForbidden();
    }

    public function test_pending_additional_cost_blocks_process_completion(): void
    {
        $import = Import::factory()->create(['status_atual' => 'em_desembaraco']);

        foreach ($import->costs as $cost) {
            $cost->update(['status_pagamento' => 'pago']);
        }

        foreach ($import->documents as $document) {
            $document->update(['status' => 'recebido_ok']);
        }

        $import->costs()->create([
            'categoria' => ImportCost::CATEGORIA_ADICIONAL,
            'tipo_custo' => ImportCost::TIPO_ADICIONAL,
            'nome' => 'Despesa pendente',
            'moeda' => 'USD',
            'status_pagamento' => 'pendente',
        ]);

        $import->refresh();
        $this->assertTrue($import->temDespesasAdicionaisPendentes());
        $this->assertNotSame('concluido', $import->status_atual);
    }

    public function test_admin_can_update_additional_cost_name_and_logs_change(): void
    {
        $admin = $this->adminUser();
        $import = Import::factory()->create();

        $cost = $import->costs()->create([
            'categoria' => ImportCost::CATEGORIA_ADICIONAL,
            'tipo_custo' => ImportCost::TIPO_ADICIONAL,
            'nome' => 'Nome original',
            'moeda' => 'USD',
            'status_pagamento' => 'pendente',
        ]);

        $this->actingAs($admin)->put(route('costs.update', $cost), [
            'nome' => 'Nome atualizado',
            'valor' => null,
            'moeda' => 'USD',
            'status_pagamento' => 'pendente',
        ])->assertRedirect(route('imports.show', $import));

        $this->assertSame('Nome atualizado', $cost->fresh()->nome);
        $this->assertTrue(
            ImportLog::where('import_id', $import->id)
                ->where('tipo_evento', 'custo_alterado')
                ->exists()
        );
    }
}
