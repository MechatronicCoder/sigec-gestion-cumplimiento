<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dimension extends Model
{
    use HasFactory;

    protected $table = 'dimensiones';

    protected $fillable = [
        'nombre',
        'descripcion',
        'peso',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'peso' => 'decimal:2',
    ];

    public function indicadores(): HasMany
    {
        return $this->hasMany(Indicador::class, 'dimension_id');
    }
}
