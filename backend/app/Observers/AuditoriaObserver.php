<?php

namespace App\Observers;

use App\Models\Auditoria;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditoriaObserver
{
    private const CAMPOS_SENSIBLES = [
        'password',
        'remember_token',
    ];

    public function created(Model $model): void
    {
        $this->registrar('creado', $model, null, $this->limpiar($model->getAttributes()));
    }

    public function updated(Model $model): void
    {
        $cambios = $model->getChanges();
        unset($cambios['updated_at']);

        if (empty($cambios)) {
            return;
        }

        $anteriores = [];
        foreach (array_keys($cambios) as $campo) {
            $anteriores[$campo] = $model->getOriginal($campo);
        }

        $this->registrar(
            'actualizado',
            $model,
            $this->limpiar($anteriores),
            $this->limpiar($cambios)
        );
    }

    public function deleted(Model $model): void
    {
        $this->registrar('eliminado', $model, $this->limpiar($model->getAttributes()), null);
    }

    private function limpiar(?array $valores): ?array
    {
        if ($valores === null) {
            return null;
        }

        foreach (self::CAMPOS_SENSIBLES as $campo) {
            if (array_key_exists($campo, $valores)) {
                $valores[$campo] = '***';
            }
        }

        return $valores;
    }

    private function registrar(string $accion, Model $model, ?array $anteriores, ?array $nuevos): void
    {
        Auditoria::create([
            'user_id' => Auth::id(),
            'accion' => $accion,
            'modelo' => class_basename($model),
            'modelo_id' => $model->getKey(),
            'valores_anteriores' => $anteriores,
            'valores_nuevos' => $nuevos,
            'created_at' => now(),
        ]);
    }
}
