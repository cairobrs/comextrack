<?php

namespace Tests\Feature;

use App\Models\Import;
use App\Models\ImportLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportCostTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): User
    {
        return User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    public function test_cost_full_update_and_log(): void
    {
        $user = $this->actingUser();
        $import = Import::factory()->create();
        $cost = $import->costs()->where('tipo_custo', 'frete_internacional')->first();
        $this->assertNotNull($cost);

        $this->actingAs($user)->put(route('costs.update', $cost), [
            'valor' => '1500.75',
            'moeda' => 'USD',
            'data_vencimento' => '2026-12-01',
            'data_pagamento' => '2026-11-15',
            'observacoes' => 'Pago antecipado com desconto',
            'status_pagamento' => 'pago',
        ])->assertRedirect(route('imports.show', $import));

        $cost->refresh();
        $this->assertEquals(1500.75, $cost->valor);
        $this->assertSame('USD', $cost->moeda);
        $this->assertSame('pago', $cost->status_pagamento);
        $this->assertNotNull($cost->data_vencimento);
        $this->assertNotNull($cost->data_pagamento);

        $this->assertTrue(
            ImportLog::where('import_id', $import->id)
                ->where('tipo_evento', 'status_custo_alterado')
                ->exists()
        );
    }
}
