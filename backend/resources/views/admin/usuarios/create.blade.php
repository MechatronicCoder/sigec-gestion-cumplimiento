<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nuevo usuario</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <form method="POST" action="{{ route('admin.usuarios.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre completo</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="mt-1 block w-full border-gray-300 rounded-md">
                        @error('name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 block w-full border-gray-300 rounded-md">
                        @error('email') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Rol</label>
                        <select name="rol" required class="mt-1 block w-full border-gray-300 rounded-md">
                            <option value="">-- Selecciona --</option>
                            <option value="admin" @selected(old('rol') === 'admin')>Administrador</option>
                            <option value="evaluador" @selected(old('rol') === 'evaluador')>Evaluador</option>
                            <option value="consulta" @selected(old('rol') === 'consulta')>Consulta</option>
                        </select>
                        @error('rol') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Contraseña inicial</label>
                        <input type="password" name="password" required class="mt-1 block w-full border-gray-300 rounded-md">
                        @error('password') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" required class="mt-1 block w-full border-gray-300 rounded-md">
                    </div>

                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('admin.usuarios.index') }}" class="px-4 py-2 text-sm text-gray-600">Cancelar</a>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
