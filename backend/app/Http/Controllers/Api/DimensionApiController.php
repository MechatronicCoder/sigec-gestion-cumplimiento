<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dimension;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class DimensionApiController extends Controller
{
    public function index()
    {
        return Dimension::orderBy('orden')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'peso'        => 'required|numeric|min:0|max:100',
            'orden'       => 'required|integer|min:0',
            'activo'      => 'boolean',
        ]);

        $dimension = Dimension::create($data);

        return response()->json($dimension, 201);
    }

    public function show(Dimension $dimension)
    {
        return $dimension;
    }

    public function update(Request $request, Dimension $dimension)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'peso'        => 'required|numeric|min:0|max:100',
            'orden'       => 'required|integer|min:0',
            'activo'      => 'boolean',
        ]);

        $dimension->update($data);

        return response()->json($dimension);
    }

    public function destroy(Dimension $dimension)
    {
        try {
            $dimension->delete();
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'No se puede eliminar: tiene indicadores asociados.',
            ], 409);
        }

        return response()->noContent();
    }
}
