<?php

namespace App\Exports;

use App\Models\Auditoria;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class AuditoriaExport implements FromArray, WithHeadings, WithTitle
{
    protected array $filtros;

    public function __construct(array $filtros = [])
    {
        $this->filtros = $filtros;
    }

    protected function query()
    {
        $query = Auditoria::with('usuario:id,name')->orderByDesc('created_at');

        if (! empty($this->filtros['modelo'])) {
            $query->where('modelo', $this->filtros['modelo']);
        }

        if (! empty($this->filtros['user_id'])) {
            $query->where('user_id', $this->filtros['user_id']);
        }

        if (! empty($this->filtros['desde'])) {
            $query->whereDate('created_at', '>=', $this->filtros['desde']);
        }

        if (! empty($this->filtros['hasta'])) {
            $query->whereDate('created_at', '<=', $this->filtros['hasta']);
        }

        return $query;
    }

    public function array(): array
    {
        $filas = [];

        foreach ($this->query()->get() as $registro) {
            $filas[] = [
                $registro->created_at->format('d/m/Y H:i:s'),
                $registro->usuario->name ?? 'Sistema',
                ucfirst($registro->accion),
                $registro->modelo,
                $registro->modelo_id,
                $registro->valores_anteriores ? json_encode($registro->valores_anteriores, JSON_UNESCAPED_UNICODE) : '',
                $registro->valores_nuevos ? json_encode($registro->valores_nuevos, JSON_UNESCAPED_UNICODE) : '',
            ];
        }

        return $filas;
    }

    public function headings(): array
    {
        return ['Fecha', 'Usuario', 'Acción', 'Modelo', 'ID', 'Valores anteriores', 'Valores nuevos'];
    }

    public function title(): string
    {
        return 'Auditoría';
    }
}
