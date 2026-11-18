<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';

    protected $fillable = [
        'indicador_id',
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

    public function indicador(): BelongsTo
    {
        return $this->belongsTo(Indicador::class, 'indicador_id');
    }

    public function evaluacionItems(): HasMany
    {
        return $this->hasMany(EvaluacionItem::class, 'item_id');
    }
}
