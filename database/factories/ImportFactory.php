<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Import>
 */
class ImportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $moeda = fake()->randomElement(['USD', 'EUR', 'BRL']);

        return [
            'numero_processo' => fake()->unique()->numerify('IMP-#######'),
            'client_id' => Client::factory(),
            'modal' => fake()->randomElement(['maritimo', 'aereo', 'rodoviario']),
            'ncm_principal' => fake()->optional()->numerify('####.##.##'),
            'descricao_mercadoria' => fake()->sentence(),
            'pais_origem' => fake()->optional()->country(),
            'porto_origem' => fake()->optional()->city(),
            'porto_destino' => fake()->optional()->city(),
            'valor_fatura' => fake()->optional()->randomFloat(2, 1000, 1000000),
            'moeda' => $moeda,
            'taxa_cambio' => $moeda === 'BRL' ? 1.0 : fake()->randomFloat(4, 4, 6),
            'data_abertura' => fake()->date(),
            'data_prevista_chegada' => fake()->optional()->date(),
            'status_atual' => fake()->randomElement(['aberto', 'em_transito', 'em_desembaraco', 'concluido', 'cancelado']),
            'observacoes' => fake()->optional()->text(),
        ];
    }
}
