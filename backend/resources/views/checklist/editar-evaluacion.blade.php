<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar evaluación</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <form method="POST" action="{{ route('checklist.evaluacion.update', $evaluacion) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre de la evaluación</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $evaluacion->nombre) }}" required
                               class="mt-1 block w-full border-gray-300 rounded-md">
                        @error('nombre') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <p class="text-xs text-gray-400">
                        Última modificación: {{ $evaluacion->updated_at->locale('es')->isoFormat('DD/MM/YYYY hh:mm A') }}
                    </p>

                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('checklist.index') }}" class="px-4 py-2 text-sm text-gray-600">Cancelar</a>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
