<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Administración — Auditoría</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <form method="GET" class="bg-white shadow rounded-lg p-4 flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs text-gray-500">Módulo</label>
                    <select name="modelo" class="mt-1 border-gray-300 rounded-md text-sm">
                        <option value="">Todos</option>
                        @foreach ($modelos as $modelo)
                            <option value="{{ $modelo }}" @selected(request('modelo') === $modelo)>{{ $modelo }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500">Usuario</label>
                    <select name="user_id" class="mt-1 border-gray-300 rounded-md text-sm">
                        <option value="">Todos</option>
                        @foreach ($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" @selected(request('user_id') == $usuario->id)>{{ $usuario->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500">Desde</label>
                    <input type="date" name="desde" value="{{ request('desde') }}" class="mt-1 border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500">Hasta</label>
                    <input type="date" name="hasta" value="{{ request('hasta') }}" class="mt-1 border-gray-300 rounded-md text-sm">
                </div>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">Filtrar</button>
                <a href="{{ route('admin.auditoria.index') }}" class="text-sm text-gray-500 hover:underline">Limpiar</a>
            </form>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase w-40">Fecha</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase w-28">Acción</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase w-32">Módulo</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Detalle</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($registros as $reg)
                            <tr>
                                <td class="px-4 py-3 text-xs text-gray-500">{{ $reg->created_at->locale('es')->isoFormat('DD/MM/YYYY hh:mm A') }}</td>
                                <td class="px-4 py-3 text-sm">{{ $reg->usuario->name ?? 'Sistema' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                        {{ $reg->accion === 'creado' ? 'bg-green-100 text-green-700' :
                                           ($reg->accion === 'eliminado' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                                        {{ ucfirst($reg->accion) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $reg->modelo }} #{{ $reg->modelo_id }}</td>
                                <td class="px-4 py-3 text-xs text-gray-500">
                                    @if ($reg->valores_nuevos)
                                        @foreach ($reg->valores_nuevos as $campo => $valor)
                                            @if (!in_array($campo, ['password', 'remember_token']))
                                                <div><strong>{{ $campo }}:</strong> {{ is_array($valor) ? json_encode($valor) : $valor }}</div>
                                            @endif
                                        @endforeach
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-400">No hay registros de auditoría todavía.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $registros->links() }}
        </div>
    </div>
</x-app-layout>
