<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Revision extends Model
{
    use HasFactory;

    protected $table = 'revisiones';

    protected $fillable = [
        'analisis_id',
        'administrador_id',
        'estado',
        'observaciones',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    /**
     * Relación con análisis
     */
    public function analisis(): BelongsTo
    {
        return $this->belongsTo(Analisis::class);
    }

    /**
     * Relación con administrador
     */
    public function administrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'administrador_id');
    }
}
