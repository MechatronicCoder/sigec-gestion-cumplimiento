<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard de cumplimiento</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if ($sinDatos)
                <div class="bg-yellow-50 text-yellow-800 px-4 py-3 rounded">
                    Todavía no hay evaluaciones registradas.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white shadow rounded-lg p-6 text-center md:col-span-1">
                        <p class="text-sm text-gray-500 uppercase tracking-wide">Cumplimiento global</p>
                        <p class="text-4xl font-bold mt-2 text-gray-800">
                            {{ $cumplimientoGlobal !== null ? $cumplimientoGlobal . '%' : '—' }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">{{ $evaluacion->nombre }}</p>
                    </div>

                    <div class="flex justify-center gap-3 mt-3">
                        <a href="{{ route('reportes.exportar.excel', $evaluacion) }}"
                        class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-700">
                            Exportar a Excel
                        </a>
                        <a href="{{ route('reportes.exportar.pdf', $evaluacion) }}"
                        class="inline-flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700">
                            Exportar a PDF
                        </a>
                    </div>

                    <div class="bg-white shadow rounded-lg p-6 md:col-span-2">
                        <h3 class="text-sm font-semibold text-gray-600 mb-3">Cumplimiento por dimensión</h3>
                        <canvas id="chartDimensiones" height="90"></canvas>
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-gray-600 mb-3">Cumplimiento por indicador</h3>
                    <canvas id="chartIndicadores" height="110"></canvas>
                </div>

                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-gray-600 mb-3">Tendencia de cumplimiento por fecha</h3>
                    @if ($tendencia->count() > 1)
                        <canvas id="chartTendencia" height="80"></canvas>
                    @else
                        <p class="text-sm text-gray-400">Se necesita más de una evaluación calificada para mostrar la tendencia.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @if (!$sinDatos)
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
        <script>
            const colorPorNivel = (pct) => {
                if (pct === null) return '#d1d5db';
                if (pct >= 90) return '#16a34a';
                if (pct >= 75) return '#84cc16';
                if (pct >= 50) return '#f59e0b';
                return '#dc2626';
            };

            // Gráfico por dimensión

            new Chart(document.getElementById('chartDimensiones'), {
                type: 'bar',
                data: {
                    labels: @json(collect($resumenDimensiones)->pluck('nombre')),
                    datasets: [{
                        data: @json(collect($resumenDimensiones)->pluck('cumplimiento')),
                        backgroundColor: @json(collect($resumenDimensiones)->pluck('cumplimiento'))
                            .map(v => v === null ? '#d1d5db' : (v >= 90 ? '#16a34a' : v >= 75 ? '#84cc16' : v >= 50 ? '#f59e0b' : '#dc2626')),
                    }]
                },
                options: {
                    indexAxis: 'y',
                    plugins: { legend: { display: false } },
                    scales: { x: { min: 0, max: 100 } }
                }
            });

            // Gráfico por indicador
            new Chart(document.getElementById('chartIndicadores'), {
                type: 'bar',
                data: {
                    labels: @json(collect($resumenIndicadores)->pluck('nombre')),
                    datasets: [{
                        data: @json(collect($resumenIndicadores)->pluck('cumplimiento')),
                        backgroundColor: @json(collect($resumenIndicadores)->pluck('cumplimiento'))
                            .map(v => v === null ? '#d1d5db' : (v >= 90 ? '#16a34a' : v >= 75 ? '#84cc16' : v >= 50 ? '#f59e0b' : '#dc2626')),
                    }]
                },
                options: {
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { min: 0, max: 100 },
                        x: { ticks: { autoSkip: false, maxRotation: 60, minRotation: 45, font: { size: 9 } } }
                    }
                }
            });

            // Gráfico de tendencia
            @if ($tendencia->count() > 1)
            new Chart(document.getElementById('chartTendencia'), {
                type: 'line',
                data: {
                    labels: @json($tendencia->pluck('fecha')),
                    datasets: [{
                        label: 'Cumplimiento global (%)',
                        data: @json($tendencia->pluck('cumplimiento')),
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        tension: 0.3,
                        fill: true,
                    }]
                },
                options: {
                    scales: { y: { min: 0, max: 100 } }
                }
            });
            @endif
        </script>
    @endif
</x-app-layout>
