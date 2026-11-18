<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Indicador;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with('indicador.dimension')->orderBy('orden')->get();
        return view('admin.items.index', compact('items'));
    }

    public function create()
    {
        $indicadores = Indicador::with('dimension')->orderBy('dimension_id')->orderBy('orden')->get();
        $indicadorSeleccionado = request()->query('indicador_id');
        return view('admin.items.create', compact('indicadores', 'indicadorSeleccionado'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'indicador_id' => 'required|exists:indicadores,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'peso' => 'required|numeric|min:0|max:100',
            'orden' => 'required|integer|min:0',
            'activo' => 'boolean',
        ]);
        $data['activo'] = $request->boolean('activo');

        Item::create($data);

        return redirect()->route('admin.items.index')->with('success', 'Ítem creado correctamente.');
    }

    public function edit(Item $item)
    {
        $indicadores = Indicador::with('dimension')->orderBy('dimension_id')->orderBy('orden')->get();
        return view('admin.items.edit', compact('item', 'indicadores'));
    }

    public function update(Request $request, Item $item)
    {
        $data = $request->validate([
            'indicador_id' => 'required|exists:indicadores,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'peso' => 'required|numeric|min:0|max:100',
            'orden' => 'required|integer|min:0',
            'activo' => 'boolean',
        ]);
        $data['activo'] = $request->boolean('activo');

        $item->update($data);

        return redirect()->route('admin.items.index')->with('success', 'Ítem actualizado correctamente.');
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()->route('admin.items.index')->with('success', 'Ítem eliminado.');
    }

    public function toggleActivo(Item $item)
    {
        $item->update(['activo' => !$item->activo]);
        return back()->with('success', 'Estado actualizado.');
    }
}
