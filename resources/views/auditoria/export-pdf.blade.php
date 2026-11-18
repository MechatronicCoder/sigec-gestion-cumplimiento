<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Historial de Auditoría - SIGEC</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        .meta { color: #555; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 4px 6px; text-align: left; vertical-align: top; }
        th { background-color: #1976d2; color: #fff; }
        tr:nth-child(even) { background-color: #f5f5f5; }
        .accion-creado { color: #2e7d32; font-weight: bold; }
        .accion-actualizado { color: #0277bd; font-weight: bold; }
        .accion-eliminado { color: #c62828; font-weight: bold; }
        .json { font-family: monospace; font-size: 9px; white-space: pre-wrap; word-break: break-word; }
    </style>
</head>
<body>
    <h1>Sistema de Gestión de Cumplimiento — Historial de Auditoría</h1>
    <div class="meta">Generado el {{ now()->format('d/m/Y H:i:s') }}</div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Acción</th>
                <th>Modelo</th>
                <th>ID</th>
                <th>Valores anteriores</th>
                <th>Valores nuevos</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($registros as $registro)
                <tr>
                    <td>{{ $registro->created_at->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $registro->usuario->name ?? 'Sistema' }}</td>
                    <td class="accion-{{ $registro->accion }}">{{ ucfirst($registro->accion) }}</td>
                    <td>{{ $registro->modelo }}</td>
                    <td>{{ $registro->modelo_id }}</td>
                    <td class="json">{{ $registro->valores_anteriores ? json_encode($registro->valores_anteriores, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : '' }}</td>
                    <td class="json">{{ $registro->valores_nuevos ? json_encode($registro->valores_nuevos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No hay registros de auditoría con los filtros aplicados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>