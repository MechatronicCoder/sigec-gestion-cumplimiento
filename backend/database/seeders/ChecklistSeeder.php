<?php

namespace Database\Seeders;

use App\Models\Dimension;
use App\Models\Indicador;
use App\Models\Item;
use Illuminate\Database\Seeder;

class ChecklistSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = database_path('seeders/data/checklist_seed_data.json');

        if (!file_exists($jsonPath)) {
            $this->command->error("No se encontró el archivo: {$jsonPath}");
            return;
        }

        $data = json_decode(file_get_contents($jsonPath), true);

        $dimensionIds = [];
        $indicadorIds = [];

        foreach ($data['dimensiones'] as $dim) {
            $registro = Dimension::firstOrCreate(
                ['nombre' => $dim['nombre']],
                [
                    'descripcion' => null,
                    'peso' => $dim['peso'],
                    'orden' => $dim['orden'],
                    'activo' => true,
                ]
            );
            $dimensionIds[$dim['codigo']] = $registro->id;
        }

        foreach ($data['indicadores'] as $ind) {
            $registro = Indicador::firstOrCreate(
                [
                    'nombre' => $ind['nombre'],
                    'dimension_id' => $dimensionIds[$ind['dimension_code']],
                ],
                [
                    'descripcion' => null,
                    'peso' => $ind['peso'],
                    'orden' => $ind['orden'],
                    'activo' => true,
                ]
            );
            $indicadorIds[$ind['codigo']] = $registro->id;
        }

        foreach ($data['items'] as $item) {
            Item::firstOrCreate(
                [
                    'nombre' => $item['nombre'],
                    'indicador_id' => $indicadorIds[$item['indicador_code']],
                ],
                [
                    'descripcion' => $item['descripcion'],
                    'peso' => 1.00,
                    'orden' => $item['orden'],
                    'activo' => true,
                ]
            );
        }

        $this->command->info('Checklist cargado: ' .
            count($data['dimensiones']) . ' dimensiones, ' .
            count($data['indicadores']) . ' indicadores, ' .
            count($data['items']) . ' items.');
    }
}
