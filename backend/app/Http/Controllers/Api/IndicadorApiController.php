<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Indicador;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class IndicadorApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Indicador::orderBy('orden');

        if ($request->filled('dimension_id')) {
            $query->where('dimension_id', $request->query('dimension_id'));
        }

        return $query->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'dimension_id' => 'required|exists:dimensiones,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'peso' => 'required|numeric|min:0|max:100',
            'orden' => 'required|integer|min:0',
            'activo' => 'boolean',
        ]);

        $indicador = Indicador::create($data);

        return response()->json($indicador, 201);
    }

    public function show(Indicador $indicador)
    {
        return $indicador;
    }

    public function update(Request $request, Indicador $indicador)
    {
        $data = $request->validate([
            'dimension_id' => 'required|exists:dimensiones,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'peso' => 'required|numeric|min:0|max:100',
            'orden' => 'required|integer|min:0',
            'activo' => 'boolean',
        ]);

        $indicador->update($data);

        return response()->json($indicador);
    }

    public function destroy(Indicador $indicador)
    {
        try {
            $indicador->delete();
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'No se puede eliminar: tiene items asociados.',
            ], 409);
        }

        return response()->noContent();
    }
}
