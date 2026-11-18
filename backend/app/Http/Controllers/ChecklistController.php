<?php

namespace App\Http\Controllers;

use App\Models\Dimension;
use App\Models\Evaluacion;
use App\Models\EvaluacionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChecklistController extends Controller
{
    public function index(Request $request)
    {
        $evaluacionAbierta = $this->obtenerEvaluacionAbierta();
        $existeAbierta = $evaluacionAbierta !== null;

        $evaluacionId = $request->query('evaluacion_id');

        $evaluacion = $evaluacionId
            ? Evaluacion::findOrFail($evaluacionId)
            : ($evaluacionAbierta ?? Evaluacion::latest('fecha_inicio')->first());

        $evaluaciones = Evaluacion::orderByDesc('fecha_inicio')->get();

        // Si nunca se ha creado ninguna evaluacion, no hay nada que mostrar todavia
        if (!$evaluacion) {
            return view('checklist.index', [
                'evaluacion' => null,
                'evaluaciones' => $evaluaciones,
                'existeAbierta' => false,
                'esEditable' => false,
                'dimensiones' => collect(),
                'calificaciones' => collect(),
            ]);
        }

        $esEditable = $evaluacionAbierta && $evaluacion->id === $evaluacionAbierta->id;

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

        return view('checklist.index', compact(
            'dimensiones', 'evaluacion', 'calificaciones', 'evaluaciones', 'esEditable', 'existeAbierta'
        ));
    }

    public function guardarTodo(Request $request)
    {
        $request->validate([
            'evaluacion_id' => 'required|exists:evaluaciones,id',
            'calificaciones' => 'nullable|array',
            'observaciones' => 'nullable|array',
        ]);

        $evaluacion = Evaluacion::findOrFail($request->evaluacion_id);

        if ($evaluacion->estado !== 'abierta') {
            abort(403, 'Esta evaluación no admite cambios.');
        }

        $calificaciones = $request->input('calificaciones', []);
        $observaciones = $request->input('observaciones', []);

        foreach ($calificaciones as $itemId => $valor) {
            EvaluacionItem::updateOrCreate(
                [
                    'evaluacion_id' => $evaluacion->id,
                    'item_id' => $itemId,
                ],
                [
                    'calificacion' => $valor !== '' ? $valor : null,
                    'observaciones' => $observaciones[$itemId] ?? null,
                    'evaluador_id' => Auth::id(),
                    'fecha_registro' => now(),
                ]
            );
        }

        $evaluacion->touch();

        return redirect()->route('checklist.index', ['evaluacion_id' => $evaluacion->id])
            ->with('success', 'Evaluación guardada. Última modificación: ' . $evaluacion->fresh()->updated_at->locale('es')->isoFormat('DD/MM/YYYY hh:mm A'));
    }

    public function editEvaluacion(Evaluacion $evaluacion)
    {
        if ($evaluacion->estado !== 'abierta') {
            abort(403, 'Solo se puede editar la evaluación abierta actual.');
        }

        return view('checklist.editar-evaluacion', compact('evaluacion'));
    }

    public function updateEvaluacion(Request $request, Evaluacion $evaluacion)
    {
        if ($evaluacion->estado !== 'abierta') {
            abort(403, 'Solo se puede editar la evaluación abierta actual.');
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $evaluacion->update(['nombre' => $request->nombre]);

        return redirect()->route('checklist.index')->with('success',
            'Evaluación actualizada. Última modificación: ' . $evaluacion->updated_at->locale('es')->isoFormat('DD/MM/YYYY hh:mm A'));
    }

    /**
     * Finaliza la evaluacion actual. NO crea una nueva evaluacion automaticamente.
     */
    public function finalizarEvaluacion(Evaluacion $evaluacion)
    {
        if ($evaluacion->estado !== 'abierta') {
            abort(403, 'Esta evaluación ya no está abierta.');
        }

        $evaluacion->update([
            'estado' => 'finalizada',
            'fecha_fin' => now()->toDateString(),
        ]);

        return redirect()->route('checklist.index')->with('success',
            'Evaluación finalizada correctamente. Usa el botón "+ Nueva evaluación" cuando quieras iniciar la siguiente.');
    }

    /**
     * Unico punto donde se crea una evaluacion nueva: accion explicita del usuario.
     */
    public function nuevaEvaluacion()
    {
        if ($this->obtenerEvaluacionAbierta()) {
            return redirect()->route('checklist.index')
                ->with('error', 'Ya existe una evaluación abierta. Finalízala antes de crear una nueva.');
        }

        $nueva = Evaluacion::create([
            'nombre' => 'Evaluación ' . now()->format('Y-m-d'),
            'fecha_inicio' => now()->toDateString(),
            'estado' => 'abierta',
            'creado_por' => Auth::id(),
        ]);

        return redirect()->route('checklist.index', ['evaluacion_id' => $nueva->id])
            ->with('success', 'Nueva evaluación creada en blanco.');
    }

    /**
     * Solo consulta si existe una evaluacion abierta. Nunca crea nada.
     */
    private function obtenerEvaluacionAbierta(): ?Evaluacion
    {
        return Evaluacion::where('estado', 'abierta')->latest('fecha_inicio')->first();
    }
}
