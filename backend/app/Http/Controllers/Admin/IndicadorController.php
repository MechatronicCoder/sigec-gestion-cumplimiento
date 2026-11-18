<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dimension;
use App\Models\Indicador;
use Illuminate\Http\Request;

class IndicadorController extends Controller
{
    public function index()
    {
        $indicadores = Indicador::with('dimension')->orderBy('orden')->withCount('items')->get();
        return view('admin.indicadores.index', compact('indicadores'));
    }

    public function create()
    {
        $dimensiones = Dimension::orderBy('orden')->get();
        $dimensionSeleccionada = request()->query('dimension_id');
        return view('admin.indicadores.create', compact('dimensiones', 'dimensionSeleccionada'));
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
        $data['activo'] = $request->boolean('activo');

        Indicador::create($data);

        return redirect()->route('admin.indicadores.index')->with('success', 'Indicador creado correctamente.');
    }

    public function edit(Indicador $indicador)
    {
        $dimensiones = Dimension::orderBy('orden')->get();
        return view('admin.indicadores.edit', compact('indicador', 'dimensiones'));
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
        $data['activo'] = $request->boolean('activo');

        $indicador->update($data);

        return redirect()->route('admin.indicadores.index')->with('success', 'Indicador actualizado correctamente.');
    }

    public function destroy(Indicador $indicador)
    {
        $indicador->delete();

        return redirect()->route('admin.indicadores.index')->with('success', 'Indicador eliminado.');
    }

    public function toggleActivo(Indicador $indicador)
    {
        $nuevoEstado = !$indicador->activo;
        $indicador->update(['activo' => $nuevoEstado]);

        if (!$nuevoEstado) {
            // Cascada: si se desactiva el indicador, se desactivan sus items
            $indicador->items()->update(['activo' => false]);
        }

        return back()->with('success', 'Estado actualizado.');
    }
}
