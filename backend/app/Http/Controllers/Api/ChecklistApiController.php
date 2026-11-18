<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dimension;
use App\Models\Evaluacion;
use App\Models\EvaluacionItem;
use App\Services\ChecklistCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChecklistApiController extends Controller
{
    /**
     * Lista todas las evaluaciones (para el selector en el frontend).
     */
    public function evaluaciones()
    {
        return Evaluacion::orderByDesc('fecha_inicio')->get();
    }

    /**
     * Crea una nueva evaluacion. Solo se permite si no hay ninguna abierta
     * (misma regla que el ChecklistController web).
     */
    public function crearEvaluacion()
    {
        if ($this->obtenerEvaluacionAbierta()) {
            return response()->json([
                'message' => 'Ya existe una evaluación abierta. Finalízala antes de crear una nueva.',
            ], 409);
        }

        $nueva = Evaluacion::create([
            'nombre' => 'Evaluación ' . now()->format('Y-m-d'),
            'fecha_inicio' => now()->toDateString(),
            'estado' => 'abierta',
            'creado_por' => Auth::id(),
        ]);

        return response()->json($nueva, 201);
    }

    /**
     * Estructura completa del checklist (dimensiones > indicadores > items)
     * junto con las calificaciones ya registradas para esa evaluacion.
     */
    public function show(Evaluacion $evaluacion)
    {
        $dimensiones = Dimension::where('activo', true)
            ->orderBy('orden')
            ->with(['indicadores' => function ($q) {
                $q->where('activo', true)->orderBy('orden')
                  ->with(['items' => function ($q2) {
                      $q2->where('activo', true)->orderBy('orden');
                  }]);
            }])
            ->get();

        $calificaciones = EvaluacionItem::where('evaluacion_id', $evaluacion->id)
            ->get()
            ->keyBy('item_id');

        $evaluacionAbierta = $this->obtenerEvaluacionAbierta();
        $esEditable = $evaluacionAbierta && $evaluacion->id === $evaluacionAbierta->id;

        return response()->json([
            'evaluacion' => $evaluacion,
            'es_editable' => $esEditable,
            'dimensiones' => $dimensiones,
            'calificaciones' => $calificaciones,
        ]);
    }

    /**
     * Guarda calificaciones/observaciones. Misma regla de negocio que
     * ChecklistController@guardarTodo: solo si la evaluacion esta abierta.
     */
    public function guardar(Request $request, Evaluacion $evaluacion)
    {
        if ($evaluacion->estado !== 'abierta') {
            return response()->json([
                'message' => 'Esta evaluación no admite cambios.',
            ], 403);
        }

        $data = $request->validate([
            'calificaciones' => 'nullable|array',
            'observaciones' => 'nullable|array',
        ]);

        $calificaciones = $data['calificaciones'] ?? [];
        $observaciones = $data['observaciones'] ?? [];

        foreach ($calificaciones as $itemId => $valor) {
            EvaluacionItem::updateOrCreate(
                [
                    'evaluacion_id' => $evaluacion->id,
                    'item_id' => $itemId,
                ],
                [
                    'calificacion' => $valor !== '' && $valor !== null ? $valor : null,
                    'observaciones' => $observaciones[$itemId] ?? null,
                    'evaluador_id' => Auth::id(),
                    'fecha_registro' => now(),
                ]
            );
        }

        $evaluacion->touch();

        return response()->json(['message' => 'Guardado correctamente.']);
    }

    /**
     * Finaliza la evaluacion (misma regla que ChecklistController@finalizarEvaluacion).
     */
    public function finalizar(Evaluacion $evaluacion)
    {
        if ($evaluacion->estado !== 'abierta') {
            return response()->json([
                'message' => 'Esta evaluación ya no está abierta.',
            ], 403);
        }

        $evaluacion->update([
            'estado' => 'finalizada',
            'fecha_fin' => now()->toDateString(),
        ]);

        return response()->json($evaluacion);
    }

    /**
     * Resumen de cumplimiento (usa el mismo ChecklistCalculator que la vista Blade).
     */
    public function resumen(Evaluacion $evaluacion)
    {
        $resumenDimensiones = ChecklistCalculator::resumenDimensiones($evaluacion);
        $resumenIndicadores = ChecklistCalculator::resumenIndicadores($evaluacion);
        $global = ChecklistCalculator::cumplimientoGlobal($resumenDimensiones);
        $nivel = ChecklistCalculator::nivelInterpretacion($global);

        return response()->json([
            'dimensiones' => $resumenDimensiones,
            'indicadores' => $resumenIndicadores,
            'cumplimiento_global' => $global,
            'nivel' => $nivel,
        ]);
    }

    private function obtenerEvaluacionAbierta(): ?Evaluacion
    {
        return Evaluacion::where('estado', 'abierta')->latest('fecha_inicio')->first();
    }
}
