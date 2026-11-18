<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Administración — Usuarios</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
                <div class="bg-green-100 text-green-800 px-4 py-3 rounded text-sm">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 text-red-800 px-4 py-3 rounded text-sm">{{ session('error') }}</div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('admin.usuarios.create') }}"
                   class="inline-flex items-center gap-1 bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">
                    + Nuevo usuario
                </a>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Correo</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase w-28">Rol</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase w-28">Estado</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase w-56">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($usuarios as $usuario)
                            <tr class="{{ !$usuario->activo ? 'bg-gray-50 text-gray-400' : '' }}">
                                <td class="px-4 py-3 text-sm font-medium">{{ $usuario->name }}</td>
                                <td class="px-4 py-3 text-sm">{{ $usuario->email }}</td>
                                <td class="px-4 py-3 text-sm capitalize">{{ $usuario->rol }}</td>
                                <td class="px-4 py-3 text-center">
                                    <form action="{{ route('admin.usuarios.toggle', $usuario) }}" method="POST" class="inline-flex items-center gap-2 justify-center">
                                        @csrf @method('PATCH')
                                        <input type="checkbox" onchange="this.form.submit()" @checked($usuario->activo)
                                               @disabled($usuario->id === auth()->id())
                                               class="rounded border-gray-300 text-indigo-600">
                                        <span class="text-xs font-medium {{ $usuario->activo ? 'text-green-600' : 'text-red-500' }}">
                                            {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </form>
                                </td>
                                <td class="px-4 py-3 text-sm text-right">
                                    <div class="inline-flex items-center gap-3">
                                        <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="text-indigo-600 hover:underline">Editar</a>
                                        <form action="{{ route('admin.usuarios.reset-password', $usuario) }}" method="POST"
                                              onsubmit="return confirm('¿Generar una nueva contraseña temporal para {{ $usuario->name }}?');">
                                            @csrf
                                            <button type="submit" class="text-amber-600 hover:underline">Reset contraseña</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
