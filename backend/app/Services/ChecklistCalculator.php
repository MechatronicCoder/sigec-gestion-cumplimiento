<?php

namespace App\Services;

use App\Models\Dimension;
use App\Models\Evaluacion;
use App\Models\EvaluacionItem;

class ChecklistCalculator
{
    /**
     * Cumplimiento agrupado por dimension para una evaluacion dada.
     * Devuelve: [ ['nombre','peso','total_items','calificados','cumplimiento'], ... ]
     */
    public static function resumenDimensiones(Evaluacion $evaluacion): array
    {
        $dimensiones = Dimension::where('activo', true)
            ->orderBy('orden')
            ->with(['indicadores' => function ($q) {
                $q->where('activo', true)->with(['items' => function ($q2) {
                    $q2->where('activo', true);
                }]);
            }])
            ->get();

        $calificaciones = EvaluacionItem::where('evaluacion_id', $evaluacion->id)
            ->pluck('calificacion', 'item_id');

        $resumen = [];

        foreach ($dimensiones as $dimension) {
            $itemIds = $dimension->indicadores->flatMap->items->pluck('id');
            $totalItems = $itemIds->count();

            $calificados = $itemIds->filter(fn ($id) => isset($calificaciones[$id]) && $calificaciones[$id] !== null);
            $sumaCalif = $calificados->sum(fn ($id) => (float) $calificaciones[$id]);

            $cumplimiento = $calificados->count() > 0
                ? round(($sumaCalif / ($calificados->count() * 5)) * 100, 1)
                : null;

            $resumen[] = [
                'nombre' => $dimension->nombre,
                'peso' => $dimension->peso,
                'total_items' => $totalItems,
                'calificados' => $calificados->count(),
                'cumplimiento' => $cumplimiento,
            ];
        }

        return $resumen;
    }

    /**
     * Cumplimiento agrupado por indicador (con su dimension) para una evaluacion dada.
     */
    public static function resumenIndicadores(Evaluacion $evaluacion): array
    {
        $dimensiones = Dimension::where('activo', true)
            ->orderBy('orden')
            ->with(['indicadores' => function ($q) {
                $q->where('activo', true)->orderBy('orden')->with(['items' => function ($q2) {
                    $q2->where('activo', true);
                }]);
            }])
            ->get();

        $calificaciones = EvaluacionItem::where('evaluacion_id', $evaluacion->id)
            ->pluck('calificacion', 'item_id');

        $resumen = [];

        foreach ($dimensiones as $dimension) {
            foreach ($dimension->indicadores as $indicador) {
                $itemIds = $indicador->items->pluck('id');
                $totalItems = $itemIds->count();

                $calificados = $itemIds->filter(fn ($id) => isset($calificaciones[$id]) && $calificaciones[$id] !== null);
                $sumaCalif = $calificados->sum(fn ($id) => (float) $calificaciones[$id]);

                $cumplimiento = $calificados->count() > 0
                    ? round(($sumaCalif / ($calificados->count() * 5)) * 100, 1)
                    : null;

                $resumen[] = [
                    'dimension' => $dimension->nombre,
                    'nombre' => $indicador->nombre,
                    'total_items' => $totalItems,
                    'calificados' => $calificados->count(),
                    'cumplimiento' => $cumplimiento,
                ];
            }
        }

        return $resumen;
    }

    /**
     * Cumplimiento global ponderado, a partir del resumen por dimension.
     */
    public static function cumplimientoGlobal(array $resumenDimensiones): ?float
    {
        $totalPeso = 0;
        $totalPonderado = 0;

        foreach ($resumenDimensiones as $dim) {
            if ($dim['cumplimiento'] !== null) {
                $totalPonderado += $dim['cumplimiento'] * $dim['peso'];
                $totalPeso += $dim['peso'];
            }
        }

        return $totalPeso > 0 ? round($totalPonderado / $totalPeso, 1) : null;
    }

    public static function nivelInterpretacion(?float $porcentaje): array
    {
        if ($porcentaje === null) {
            return ['label' => 'Sin datos', 'color' => 'gray'];
        }
        if ($porcentaje >= 90) {
            return ['label' => 'Óptimo', 'color' => 'green'];
        }
        if ($porcentaje >= 75) {
            return ['label' => 'Bajo', 'color' => 'lime'];
        }
        if ($porcentaje >= 50) {
            return ['label' => 'Moderado', 'color' => 'amber'];
        }
        return ['label' => 'Crítico', 'color' => 'red'];
    }
}
