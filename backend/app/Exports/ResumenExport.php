<?php

namespace App\Exports;

use App\Models\Evaluacion;
use App\Services\ChecklistCalculator;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class ResumenExport implements WithMultipleSheets
{
    protected Evaluacion $evaluacion;

    public function __construct(Evaluacion $evaluacion)
    {
        $this->evaluacion = $evaluacion;
    }

    public function sheets(): array
    {
        return [
            'Dimensiones' => new ResumenDimensionesSheet($this->evaluacion),
            'Indicadores' => new ResumenIndicadoresSheet($this->evaluacion),
        ];
    }
}

class ResumenDimensionesSheet implements FromArray, WithHeadings, WithTitle
{
    protected Evaluacion $evaluacion;

    protected array $resumenDimensiones;

    protected ?float $cumplimientoGlobal;

    public function __construct(Evaluacion $evaluacion)
    {
        $this->evaluacion = $evaluacion;
        $this->resumenDimensiones = ChecklistCalculator::resumenDimensiones($evaluacion);
        $this->cumplimientoGlobal = ChecklistCalculator::cumplimientoGlobal($this->resumenDimensiones);
    }

    public function array(): array
    {
        $filas = [];

        foreach ($this->resumenDimensiones as $dim) {
            $nivel = ChecklistCalculator::nivelInterpretacion($dim['cumplimiento']);
            $filas[] = [
                $dim['nombre'],
                $dim['peso'].'%',
                $dim['calificados'].' / '.$dim['total_items'],
                $dim['cumplimiento'] !== null ? $dim['cumplimiento'].'%' : 'Sin calificar',
                $nivel['label'],
            ];
        }

        $filas[] = ['', '', '', '', ''];
        $filas[] = [
            'CUMPLIMIENTO GLOBAL',
            '',
            '',
            $this->cumplimientoGlobal !== null ? $this->cumplimientoGlobal.'%' : '—',
            ChecklistCalculator::nivelInterpretacion($this->cumplimientoGlobal)['label'],
        ];

        return $filas;
    }

    public function headings(): array
    {
        return ['Dimensión', 'Peso', 'Ítems calificados', 'Cumplimiento', 'Nivel'];
    }

    public function title(): string
    {
        return 'Dimensiones';
    }
}

class ResumenIndicadoresSheet implements FromArray, WithHeadings, WithTitle
{
    protected array $resumenIndicadores;

    public function __construct(Evaluacion $evaluacion)
    {
        $this->resumenIndicadores = ChecklistCalculator::resumenIndicadores($evaluacion);
    }

    public function array(): array
    {
        $filas = [];

        foreach ($this->resumenIndicadores as $ind) {
            $nivel = ChecklistCalculator::nivelInterpretacion($ind['cumplimiento']);
            $filas[] = [
                $ind['nombre'],
                $ind['dimension'],
                $ind['calificados'].' / '.$ind['total_items'],
                $ind['cumplimiento'] !== null ? $ind['cumplimiento'].'%' : 'Sin calificar',
                $nivel['label'],
            ];
        }

        return $filas;
    }

    public function headings(): array
    {
        return ['Indicador', 'Dimensión', 'Ítems calificados', 'Cumplimiento', 'Nivel'];
    }

    public function title(): string
    {
        return 'Indicadores';
    }
}
