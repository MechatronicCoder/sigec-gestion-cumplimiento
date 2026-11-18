<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reportes — Histórico de evaluaciones</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="text-sm">
                <a href="{{ route('reportes.resumen') }}" class="text-indigo-600 hover:underline">← Volver al resumen actual</a>
            </div>

            @if ($historico->isEmpty())
                <div class="bg-yellow-50 text-yellow-800 px-4 py-3 rounded">
                    Todavía no hay evaluaciones registradas.
                </div>
            @else
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Evaluación</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase w-32">Fecha inicio</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase w-28">Estado</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase w-40">Cumplimiento</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase w-24">Detalle</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($historico as $fila)
                                @php $nivel = \App\Http\Controllers\ReporteController::nivelInterpretacion($fila['cumplimiento']); @endphp
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $fila['evaluacion']->nombre }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $fila['evaluacion']->fecha_inicio->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="{{ $fila['evaluacion']->estado === 'abierta' ? 'text-green-600' : 'text-gray-500' }}">
                                            {{ ucfirst($fila['evaluacion']->estado) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="font-semibold text-{{ $nivel['color'] }}-600">
                                            {{ $fila['cumplimiento'] !== null ? $fila['cumplimiento'] . '%' : '—' }}
                                        </span>
                                        <span class="text-xs text-gray-400 ml-1">({{ $nivel['label'] }})</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right">
                                        <a href="{{ route('reportes.show', $fila['evaluacion']) }}" class="text-indigo-600 hover:underline">Ver</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
