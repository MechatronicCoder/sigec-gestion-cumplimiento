<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Indicador extends Model
{
    use HasFactory;

    protected $table = 'indicadores';

    protected $fillable = [
        'dimension_id',
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

    public function dimension(): BelongsTo
    {
        return $this->belongsTo(Dimension::class, 'dimension_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'indicador_id');
    }
}
