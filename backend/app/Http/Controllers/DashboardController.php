<?php

namespace App\Http\Controllers;

use App\Models\Evaluacion;
use App\Services\ChecklistCalculator;

class DashboardController extends Controller
{
    public function index()
    {
        $evaluacion = Evaluacion::latest('fecha_inicio')->first();

        if (!$evaluacion) {
            return view('dashboard', [
                'sinDatos' => true,
            ]);
        }

        $resumenDimensiones = ChecklistCalculator::resumenDimensiones($evaluacion);
        $cumplimientoGlobal = ChecklistCalculator::cumplimientoGlobal($resumenDimensiones);
        $resumenIndicadores = ChecklistCalculator::resumenIndicadores($evaluacion);

        $evaluaciones = Evaluacion::orderBy('fecha_inicio')->get();
        $tendencia = $evaluaciones->map(function ($ev) {
            $resumen = ChecklistCalculator::resumenDimensiones($ev);
            return [
                'fecha' => $ev->fecha_inicio->format('d/m/Y'),
                'cumplimiento' => ChecklistCalculator::cumplimientoGlobal($resumen),
            ];
        })->filter(fn ($t) => $t['cumplimiento'] !== null)->values();

        return view('dashboard', [
            'sinDatos' => false,
            'evaluacion' => $evaluacion,
            'cumplimientoGlobal' => $cumplimientoGlobal,
            'resumenDimensiones' => $resumenDimensiones,
            'resumenIndicadores' => $resumenIndicadores,
            'tendencia' => $tendencia,
        ]);
    }
}
