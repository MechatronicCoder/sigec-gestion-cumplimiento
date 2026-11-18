<?php

namespace App\Http\Controllers;

use App\Models\Evaluacion;
use App\Services\ChecklistCalculator;
use App\Exports\ResumenExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    public function resumen()
    {
        $evaluacion = Evaluacion::latest('fecha_inicio')->first();

        if (!$evaluacion) {
            return view('reportes.resumen', [
                'evaluacion' => null,
                'resumenDimensiones' => [],
                'resumenIndicadores' => [],
                'cumplimientoGlobal' => null,
            ]);
        }

        $resumenDimensiones = ChecklistCalculator::resumenDimensiones($evaluacion);
        $resumenIndicadores = ChecklistCalculator::resumenIndicadores($evaluacion);
        $cumplimientoGlobal = ChecklistCalculator::cumplimientoGlobal($resumenDimensiones);

        return view('reportes.resumen', compact('evaluacion', 'resumenDimensiones', 'resumenIndicadores', 'cumplimientoGlobal'));
    }

    public function historico()
    {
        $evaluaciones = Evaluacion::orderByDesc('fecha_inicio')->get();

        $historico = $evaluaciones->map(function ($evaluacion) {
            $resumenDimensiones = ChecklistCalculator::resumenDimensiones($evaluacion);
            return [
                'evaluacion' => $evaluacion,
                'cumplimiento' => ChecklistCalculator::cumplimientoGlobal($resumenDimensiones),
            ];
        });

        return view('reportes.historico', compact('historico'));
    }

    public function show(Evaluacion $evaluacion)
    {
        $resumenDimensiones = ChecklistCalculator::resumenDimensiones($evaluacion);
        $resumenIndicadores = ChecklistCalculator::resumenIndicadores($evaluacion);
        $cumplimientoGlobal = ChecklistCalculator::cumplimientoGlobal($resumenDimensiones);

        return view('reportes.resumen', compact('evaluacion', 'resumenDimensiones', 'resumenIndicadores', 'cumplimientoGlobal'));
    }

    public static function nivelInterpretacion(?float $porcentaje): array
    {
        return ChecklistCalculator::nivelInterpretacion($porcentaje);
    }

    // ... dentro de la clase ReporteController, agrega:

    public function exportExcel(Evaluacion $evaluacion)
    {
        $nombreArchivo = 'sigec-resumen-' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new ResumenExport($evaluacion), $nombreArchivo);
    }

    public function exportPdf(Evaluacion $evaluacion)
    {
        $resumenDimensiones = ChecklistCalculator::resumenDimensiones($evaluacion);
        $resumenIndicadores = ChecklistCalculator::resumenIndicadores($evaluacion);
        $cumplimientoGlobal = ChecklistCalculator::cumplimientoGlobal($resumenDimensiones);

        $pdf = Pdf::loadView('reportes.resumen-pdf', compact('evaluacion', 'resumenDimensiones', 'resumenIndicadores', 'cumplimientoGlobal'));

        $nombreArchivo = 'sigec-resumen-' . now()->format('Y-m-d_H-i-s') . '.pdf';
        return $pdf->download($nombreArchivo);
    }
}
