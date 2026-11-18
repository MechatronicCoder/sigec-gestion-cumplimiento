<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class ItemApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::orderBy('orden');

        if ($request->filled('indicador_id')) {
            $query->where('indicador_id', $request->query('indicador_id'));
        }

        return $query->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'indicador_id' => 'required|exists:indicadores,id',
            'nombre'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string',
            'peso'         => 'required|numeric|min:0|max:100',
            'orden'        => 'required|integer|min:0',
            'activo'       => 'boolean',
        ]);

        $item = Item::create($data);

        return response()->json($item, 201);
    }

    public function show(Item $item)
    {
        return $item;
    }

    public function update(Request $request, Item $item)
    {
        $data = $request->validate([
            'indicador_id' => 'required|exists:indicadores,id',
            'nombre'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string',
            'peso'         => 'required|numeric|min:0|max:100',
            'orden'        => 'required|integer|min:0',
            'activo'       => 'boolean',
        ]);

        $item->update($data);

        return response()->json($item);
    }

    public function destroy(Item $item)
    {
        try {
            $item->delete();
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'No se puede eliminar: tiene evaluaciones asociadas.',
            ], 409);
        }

        return response()->noContent();
    }
}
