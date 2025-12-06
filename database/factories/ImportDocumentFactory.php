<?php

namespace Database\Factories;

use App\Models\Import;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ImportDocument>
 */
class ImportDocumentFactory extends Factory
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
            'tipo_documento' => fake()->randomElement([
                'DI',
                'BL/AWB',
                'Invoice',
                'Packing List',
                'Certificado de Origem',
                'Licença de Importação',
            ]),
            'arquivo' => fake()->filePath(),
            'observacoes' => fake()->optional()->text(),
        ];
    }
}
