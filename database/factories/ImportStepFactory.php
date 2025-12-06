<?php

namespace Database\Factories;

use App\Models\Import;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ImportStep>
 */
class ImportStepFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'import_id' => Import::factory(),
            'nome_etapa' => fake()->randomElement([
                'Emissão de DI',
                'Desembaraço Aduaneiro',
                'Pagamento de Impostos',
                'Liberação de Carga',
                'Entrega Final',
            ]),
            'data_prevista' => fake()->optional()->date(),
            'data_realizada' => fake()->optional()->date(),
            'responsavel' => fake()->optional()->name(),
            'observacoes' => fake()->optional()->text(),
        ];
    }
}
