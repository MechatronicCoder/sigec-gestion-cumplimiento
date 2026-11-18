<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dimension;
use Illuminate\Http\Request;

class DimensionController extends Controller
{
    public function index()
    {
        $dimensiones = Dimension::orderBy('orden')->withCount('indicadores')->get();
        return view('admin.dimensiones.index', compact('dimensiones'));
    }

    public function create()
    {
        return view('admin.dimensiones.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'peso' => 'required|numeric|min:0|max:100',
            'orden' => 'required|integer|min:0',
            'activo' => 'boolean',
        ]);
        $data['activo'] = $request->boolean('activo');

        Dimension::create($data);

        return redirect()->route('admin.dimensiones.index')->with('success', 'Dimensión creada correctamente.');
    }

    public function edit(Dimension $dimension)
    {
        return view('admin.dimensiones.edit', compact('dimension'));
    }

    public function update(Request $request, Dimension $dimension)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'peso' => 'required|numeric|min:0|max:100',
            'orden' => 'required|integer|min:0',
            'activo' => 'boolean',
        ]);
        $data['activo'] = $request->boolean('activo');

        $dimension->update($data);

        return redirect()->route('admin.dimensiones.index')->with('success', 'Dimensión actualizada correctamente.');
    }

    public function destroy(Dimension $dimension)
    {
        // Los indicadores e items asociados se eliminan en cascada (definido en la migracion)
        $dimension->delete();

        return redirect()->route('admin.dimensiones.index')->with('success', 'Dimensión eliminada.');
    }

    public function toggleActivo(Dimension $dimension)
    {
        $nuevoEstado = !$dimension->activo;
        $dimension->update(['activo' => $nuevoEstado]);

        if (!$nuevoEstado) {
            // Cascada: si se desactiva la dimensión, se desactivan sus indicadores y los items de esos indicadores
            $indicadorIds = $dimension->indicadores()->pluck('id');
            $dimension->indicadores()->update(['activo' => false]);
            \App\Models\Item::whereIn('indicador_id', $indicadorIds)->update(['activo' => false]);
        }

        return back()->with('success', 'Estado actualizado.');
    }
}
