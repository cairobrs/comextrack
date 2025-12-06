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
            'nome_fantasia' => fake()->company(),
            'razao_social' => fake()->optional()->company(),
            'cnpj' => fake()->optional()->numerify('##############'),
            'email' => fake()->optional()->companyEmail(),
            'telefone' => fake()->optional()->phoneNumber(),
            'contato_responsavel' => fake()->optional()->name(),
            'observacoes' => fake()->optional()->text(),
        ];
    }
}
