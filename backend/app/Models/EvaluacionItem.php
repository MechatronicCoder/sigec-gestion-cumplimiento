<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluacionItem extends Model
{
    use HasFactory;

    protected $table = 'evaluacion_items';

    protected $fillable = [
        'evaluacion_id',
        'item_id',
        'calificacion',
        'observaciones',
        'evaluador_id',
        'fecha_registro',
    ];

    protected $casts = [
        'calificacion' => 'decimal:2',
        'fecha_registro' => 'datetime',
    ];

    public function evaluacion(): BelongsTo
    {
        return $this->belongsTo(Evaluacion::class, 'evaluacion_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function evaluador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluador_id');
    }
}
