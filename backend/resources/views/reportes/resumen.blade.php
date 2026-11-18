<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reportes — Resumen de la última evaluación</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="text-sm">
                <a href="{{ route('reportes.historico') }}" class="text-indigo-600 hover:underline">Ver histórico de evaluaciones →</a>
            </div>
            @if (!$evaluacion)
                <div class="bg-yellow-50 text-yellow-800 px-4 py-3 rounded">
                    Todavía no hay ninguna evaluación registrada.
                </div>
            @else
                <div class="bg-white shadow rounded-lg p-6">
                    <p class="text-sm text-gray-500">Evaluación</p>
                    <h3 class="text-lg font-semibold text-gray-800">{{ $evaluacion->nombre }}</h3>
                    <p class="text-sm text-gray-400">
                        Iniciada el {{ $evaluacion->fecha_inicio->format('d/m/Y') }}
                        — Estado:
                        <span class="{{ $evaluacion->estado === 'abierta' ? 'text-green-600' : 'text-gray-600' }} font-medium">
                            {{ ucfirst($evaluacion->estado) }}
                        </span>
                    </p>
                </div>

                {{-- Cumplimiento global --}}
                @php $nivelGlobal = \App\Http\Controllers\ReporteController::nivelInterpretacion($cumplimientoGlobal); @endphp
                <div class="bg-white shadow rounded-lg p-6 text-center">
                    <p class="text-sm text-gray-500 uppercase tracking-wide">Cumplimiento global</p>
                    <p class="text-5xl font-bold mt-2 text-{{ $nivelGlobal['color'] }}-600">
                        {{ $cumplimientoGlobal !== null ? $cumplimientoGlobal . '%' : '—' }}
                    </p>
                    <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-medium bg-{{ $nivelGlobal['color'] }}-100 text-{{ $nivelGlobal['color'] }}-700">
                        {{ $nivelGlobal['label'] }}
                    </span>
                </div>

                <div class="flex justify-center gap-3">

                <a href="{{ route('reportes.exportar.excel', $evaluacion) }}"
                class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-700">
                    Exportar a Excel
                </a>
                <a href="{{ route('reportes.exportar.pdf', $evaluacion) }}"
                class="inline-flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700">
                    Exportar a PDF
                </a>
                </div>
                {{-- Cumplimiento por dimensión --}}
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-3 bg-gray-800 text-white">
                        <h3 class="font-semibold">Cumplimiento por dimensión</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach ($resumenDimensiones as $dim)
                            @php $nivel = \App\Http\Controllers\ReporteController::nivelInterpretacion($dim['cumplimiento']); @endphp
                            <div class="px-6 py-4">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium text-gray-700">{{ $dim['nombre'] }}</span>
                                    <span class="text-sm font-semibold text-{{ $nivel['color'] }}-600">
                                        {{ $dim['cumplimiento'] !== null ? $dim['cumplimiento'] . '%' : 'Sin calificar' }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5">
                                    <div class="bg-{{ $nivel['color'] }}-500 h-2.5 rounded-full"
                                         style="width: {{ $dim['cumplimiento'] ?? 0 }}%"></div>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $dim['calificados'] }} de {{ $dim['total_items'] }} ítems calificados · Peso: {{ $dim['peso'] }}%
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                                {{-- Cumplimiento por indicador --}}
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-3 bg-gray-800 text-white">
                        <h3 class="font-semibold">Cumplimiento por indicador</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach ($resumenIndicadores as $ind)
                            @php $nivel = \App\Http\Controllers\ReporteController::nivelInterpretacion($ind['cumplimiento']); @endphp
                            <div class="px-6 py-4">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium text-gray-700">{{ $ind['nombre'] }}</span>
                                    <span class="text-sm font-semibold text-{{ $nivel['color'] }}-600">
                                        {{ $ind['cumplimiento'] !== null ? $ind['cumplimiento'] . '%' : 'Sin calificar' }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5">
                                    <div class="bg-{{ $nivel['color'] }}-500 h-2.5 rounded-full"
                                         style="width: {{ $ind['cumplimiento'] ?? 0 }}%"></div>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $ind['calificados'] }} de {{ $ind['total_items'] }} ítems calificados · {{ $ind['dimension'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                {{-- Tabla de interpretación --}}
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-3 bg-gray-100">
                        <h3 class="text-sm font-semibold text-gray-600">Tabla de interpretación de resultados</h3>
                    </div>
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            <tr>
                                <td class="px-6 py-2 font-medium text-green-600">≥ 90%</td>
                                <td class="px-6 py-2">Óptimo</td>
                                <td class="px-6 py-2 text-gray-500">Controles implementados y funcionando.</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-2 font-medium text-lime-600">75% – 89%</td>
                                <td class="px-6 py-2">Bajo</td>
                                <td class="px-6 py-2 text-gray-500">Controles mayormente implementados.</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-2 font-medium text-amber-600">50% – 74%</td>
                                <td class="px-6 py-2">Moderado</td>
                                <td class="px-6 py-2 text-gray-500">Brechas identificadas, plan de mejora prioritario.</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-2 font-medium text-red-600">&lt; 50%</td>
                                <td class="px-6 py-2">Crítico</td>
                                <td class="px-6 py-2 text-gray-500">Acción inmediata requerida.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
