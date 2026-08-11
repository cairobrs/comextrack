<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Import;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): User
    {
        return User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    public function test_authenticated_user_can_create_import_with_default_documents_and_costs(): void
    {
        $user = $this->actingUser();
        $client = Client::factory()->create();

        $response = $this->actingAs($user)->post(route('imports.store'), [
            'numero_processo' => 'PROC-999888',
            'client_id' => $client->id,
            'modal' => 'maritimo',
            'ncm_principal' => '1234.56.78',
            'descricao_mercadoria' => 'Mercadoria teste',
            'pais_origem' => 'China',
            'valor_fatura' => '1000.50',
            'moeda' => 'USD',
            'data_abertura' => '2025-06-01',
            'data_prevista_chegada' => null,
            'status_atual' => 'aberto',
            'observacoes' => null,
        ]);

        $response->assertRedirect(route('imports.index'));
        $import = Import::where('numero_processo', 'PROC-999888')->first();
        $this->assertNotNull($import);
        $this->assertSame('1234.56.78', $import->ncm_principal);
        $this->assertCount(4, $import->documents);
        $this->assertCount(4, $import->costs);
    }

    public function test_authenticated_user_can_view_edit_and_delete_import(): void
    {
        $user = $this->actingUser();
        $import = Import::factory()->create();

        $this->actingAs($user)->get(route('imports.show', $import))->assertOk();

        $this->actingAs($user)->get(route('imports.edit', $import))->assertOk();

        $this->actingAs($user)->put(route('imports.update', $import), [
            'numero_processo' => $import->numero_processo,
            'client_id' => $import->client_id,
            'modal' => $import->modal,
            'ncm_principal' => '8765.43.21',
            'descricao_mercadoria' => $import->descricao_mercadoria,
            'pais_origem' => $import->pais_origem,
            'valor_fatura' => $import->valor_fatura,
            'moeda' => $import->moeda ?? 'USD',
            'data_abertura' => $import->data_abertura->format('Y-m-d'),
            'data_prevista_chegada' => $import->data_prevista_chegada?->format('Y-m-d'),
            'status_atual' => $import->status_atual,
            'observacoes' => $import->observacoes,
        ])->assertRedirect(route('imports.index'));

        $this->assertDatabaseHas('imports', [
            'id' => $import->id,
            'ncm_principal' => '8765.43.21',
        ]);

        $this->actingAs($user)->delete(route('imports.destroy', $import))->assertRedirect(route('imports.index'));
        $this->assertDatabaseMissing('imports', ['id' => $import->id]);
    }

    public function test_import_index_filters_work(): void
    {
        $user = $this->actingUser();
        $clientA = Client::factory()->create(['nome_cliente' => 'Cliente A']);
        $clientB = Client::factory()->create(['nome_cliente' => 'Cliente B']);

        $importA = Import::factory()->create([
            'client_id' => $clientA->id,
            'numero_processo' => 'PROC-111222',
            'status_atual' => 'aberto',
            'moeda' => 'BRL',
        ]);
        Import::factory()->create([
            'client_id' => $clientB->id,
            'numero_processo' => 'PROC-333444',
            'status_atual' => 'concluido',
            'moeda' => 'BRL',
        ]);

        $this->actingAs($user)
            ->get(route('imports.index', ['client_id' => $clientA->id]))
            ->assertOk()
            ->assertSee('PROC-111222')
            ->assertDontSee('PROC-333444');

        $this->actingAs($user)
            ->get(route('imports.index', ['status_atual' => 'aberto']))
            ->assertOk()
            ->assertSee('PROC-111222');

        $this->actingAs($user)
            ->get(route('imports.index', ['search_process_number' => '111-222']))
            ->assertOk()
            ->assertSee('PROC-111222');
    }
}
