<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #333; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .subtitulo { color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #1f2937; color: #fff; }
        .global { text-align: center; margin: 20px 0; padding: 15px; border: 2px solid #333; }
        .global .pct { font-size: 32px; font-weight: bold; }
        .nivel-optimo { color: #16a34a; }
        .nivel-bajo { color: #84cc16; }
        .nivel-moderado { color: #f59e0b; }
        .nivel-critico { color: #dc2626; }
        .nivel-sindatos { color: #9ca3af; }
        footer { margin-top: 30px; font-size: 10px; color: #999; text-align: center; }
    </style>
</head>
<body>
    <h1>SIGEC — Checklist de Ciberseguridad</h1>
    <p class="subtitulo">
        Evaluación: <strong>{{ $evaluacion->nombre }}</strong><br>
        Fecha de inicio: {{ $evaluacion->fecha_inicio->locale('es')->isoFormat('DD/MM/YYYY') }} —
        Estado: {{ ucfirst($evaluacion->estado) }}<br>
        Generado el {{ now()->locale('es')->isoFormat('DD/MM/YYYY hh:mm:ss A') }}
    </p>

    @php
        $nivelGlobal = \App\Services\ChecklistCalculator::nivelInterpretacion($cumplimientoGlobal);
        $clasesNivel = [
            'Óptimo' => 'nivel-optimo',
            'Bajo' => 'nivel-bajo',
            'Moderado' => 'nivel-moderado',
            'Crítico' => 'nivel-critico',
            'Sin datos' => 'nivel-sindatos',
        ];
    @endphp
    <div class="global">
        <div>CUMPLIMIENTO GLOBAL</div>
        <div class="pct {{ $clasesNivel[$nivelGlobal['label']] ?? '' }}">
            {{ $cumplimientoGlobal !== null ? $cumplimientoGlobal . '%' : '—' }}
        </div>
        <div>{{ $nivelGlobal['label'] }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Dimensión</th>
                <th>Peso</th>
                <th>Ítems calificados</th>
                <th>Cumplimiento</th>
                <th>Nivel</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($resumenDimensiones as $dim)
                @php $nivel = \App\Services\ChecklistCalculator::nivelInterpretacion($dim['cumplimiento']); @endphp
                <tr>
                    <td>{{ $dim['nombre'] }}</td>
                    <td>{{ $dim['peso'] }}%</td>
                    <td>{{ $dim['calificados'] }} / {{ $dim['total_items'] }}</td>
                    <td>{{ $dim['cumplimiento'] !== null ? $dim['cumplimiento'] . '%' : 'Sin calificar' }}</td>
                    <td>{{ $nivel['label'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="margin-top: 20px;">
        <thead>
            <tr>
                <th>Indicador</th>
                <th>Dimensión</th>
                <th>Ítems calificados</th>
                <th>Cumplimiento</th>
                <th>Nivel</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($resumenIndicadores as $ind)
                @php $nivelInd = \App\Services\ChecklistCalculator::nivelInterpretacion($ind['cumplimiento']); @endphp
                <tr>
                    <td>{{ $ind['nombre'] }}</td>
                    <td>{{ $ind['dimension'] }}</td>
                    <td>{{ $ind['calificados'] }} / {{ $ind['total_items'] }}</td>
                    <td>{{ $ind['cumplimiento'] !== null ? $ind['cumplimiento'] . '%' : 'Sin calificar' }}</td>
                    <td>{{ $nivelInd['label'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="margin-top: 25px;">
        <thead>
            <tr><th colspan="3">Tabla de interpretación de resultados</th></tr>
        </thead>
        <tbody>
            <tr><td>&ge; 90%</td><td>Óptimo</td><td>Controles implementados y funcionando.</td></tr>
            <tr><td>75% – 89%</td><td>Bajo</td><td>Controles mayormente implementados.</td></tr>
            <tr><td>50% – 74%</td><td>Moderado</td><td>Brechas identificadas, plan de mejora prioritario.</td></tr>
            <tr><td>&lt; 50%</td><td>Crítico</td><td>Acción inmediata requerida.</td></tr>
        </tbody>
    </table>

    <footer>SIGEC — Sistema de Gestión de Evaluación de Ciberseguridad</footer>
</body>
</html>
