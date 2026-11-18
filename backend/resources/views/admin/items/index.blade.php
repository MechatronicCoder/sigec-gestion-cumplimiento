<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Administración — Ítems</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
                <div class="bg-green-100 text-green-800 px-4 py-3 rounded text-sm">{{ session('success') }}</div>
            @endif

            <div class="flex flex-wrap gap-3 justify-between items-center">
                <div class="space-x-4 text-sm text-gray-500">
                    <a href="{{ route('admin.dimensiones.index') }}" class="hover:text-indigo-600">Dimensiones</a>
                    <a href="{{ route('admin.indicadores.index') }}" class="hover:text-indigo-600">Indicadores</a>
                    <span class="font-medium text-gray-800">Ítems</span>
                </div>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('admin.items.create') }}"
                   class="inline-flex items-center gap-1 bg-indigo-600 text-green px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700 shadow-sm">
                    <span class="text-lg leading-none">+</span> Nuevo ítem
                </a>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase w-16">Orden</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dimensión / Indicador</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ítem</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase w-32">Estado</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase w-36">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($items as $item)
                            <tr class="{{ !$item->activo ? 'bg-gray-50 text-gray-400' : '' }}">
                                <td class="px-4 py-3 text-sm">{{ $item->orden }}</td>
                                <td class="px-4 py-3 text-xs text-gray-500">
                                    {{ $item->indicador->dimension->nombre }}<br>
                                    <span class="text-gray-400">{{ $item->indicador->nombre }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $item->nombre }}</td>
                                <td class="px-4 py-3 text-center">
                                    <form action="{{ route('admin.items.toggle', $item) }}" method="POST" class="inline-flex items-center gap-2 justify-center">
                                        @csrf @method('PATCH')
                                        <input type="checkbox" onchange="this.form.submit()" @checked($item->activo)
                                               class="rounded border-gray-300 text-indigo-600">
                                        <span class="text-xs font-medium {{ $item->activo ? 'text-green-600' : 'text-red-500' }}">
                                            {{ $item->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </form>
                                </td>
                                <td class="px-4 py-3 text-sm text-right">
                                    @if ($item->activo)
                                        <div class="inline-flex items-center gap-3">
                                            <a href="{{ route('admin.items.edit', $item) }}" class="text-indigo-600 hover:underline">Editar</a>
                                            <form action="{{ route('admin.items.destroy', $item) }}" method="POST"
                                                  onsubmit="return confirm('¿Eliminar este ítem?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-gray-300 text-xs italic">Sin acciones</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
