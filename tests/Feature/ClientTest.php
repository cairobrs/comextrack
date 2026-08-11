<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): User
    {
        return User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    public function test_authenticated_user_can_list_clients(): void
    {
        Client::factory()->count(2)->create();

        $response = $this->actingAs($this->actingUser())->get(route('clients.index'));

        $response->assertOk();
    }

    public function test_authenticated_user_can_create_client(): void
    {
        $user = $this->actingUser();

        $response = $this->actingAs($user)->post(route('clients.store'), [
            'nome_cliente' => 'Empresa Teste LTDA',
            'cnpj' => '12345678000199',
            'email' => 'contato@empresa.test',
            'nome_responsavel' => 'João Silva',
            'telefone_responsavel' => '11999999999',
            'observacoes' => 'Obs',
        ]);

        $response->assertRedirect(route('clients.index'));
        $this->assertDatabaseHas('clients', [
            'nome_cliente' => 'Empresa Teste LTDA',
            'email' => 'contato@empresa.test',
        ]);
    }

    public function test_authenticated_user_can_edit_and_delete_client(): void
    {
        $user = $this->actingUser();
        $client = Client::factory()->create(['nome_cliente' => 'Antigo']);

        $this->actingAs($user)->get(route('clients.edit', $client))->assertOk();

        $this->actingAs($user)->put(route('clients.update', $client), [
            'nome_cliente' => 'Novo Nome',
            'cnpj' => null,
            'email' => null,
            'nome_responsavel' => null,
            'telefone_responsavel' => null,
            'observacoes' => null,
        ])->assertRedirect(route('clients.index'));

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'nome_cliente' => 'Novo Nome',
        ]);

        $this->actingAs($user)->delete(route('clients.destroy', $client))->assertRedirect(route('clients.index'));
        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }

    public function test_store_client_requires_nome_cliente(): void
    {
        $user = $this->actingUser();

        $response = $this->actingAs($user)->post(route('clients.store'), [
            'nome_cliente' => '',
        ]);

        $response->assertSessionHasErrors('nome_cliente');
    }
}
