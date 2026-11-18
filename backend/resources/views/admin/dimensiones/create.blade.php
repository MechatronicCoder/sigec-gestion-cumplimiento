<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva dimensión</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <form method="POST" action="{{ route('admin.dimensiones.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required
                               class="mt-1 block w-full border-gray-300 rounded-md">
                        @error('nombre') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Descripción</label>
                        <textarea name="descripcion" rows="3" class="mt-1 block w-full border-gray-300 rounded-md">{{ old('descripcion') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Peso (%)</label>
                            <input type="number" step="0.01" name="peso" value="{{ old('peso', 0) }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md">
                            @error('peso') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Orden</label>
                            <input type="number" name="orden" value="{{ old('orden', 0) }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="activo" value="1" checked
                               class="rounded border-gray-300">
                        <label class="ml-2 text-sm text-gray-700">Activo</label>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('admin.dimensiones.index') }}" class="px-4 py-2 text-sm text-gray-600">Cancelar</a>
                        <button type="submit" class="bg-indigo-600 text-gray-600 px-4 py-2 rounded text-sm hover:bg-indigo-700">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
