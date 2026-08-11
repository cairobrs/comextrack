<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome_cliente' => fake()->company(),
            'cnpj' => fake()->optional()->numerify('##############'),
            'email' => fake()->optional()->companyEmail(),
            'telefone' => fake()->optional()->phoneNumber(),
            'nome_responsavel' => fake()->optional()->name(),
            'telefone_responsavel' => fake()->optional()->phoneNumber(),
            'observacoes' => fake()->optional()->text(),
        ];
    }
}
