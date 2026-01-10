<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialResultado extends Model
{
    use HasFactory;

    protected $table = 'historial_resultados';

    protected $fillable = [
        'resultado_id',
        'valor_anterior',
        'valor_nuevo',
        'usuario_id',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    /**
     * Relación con resultado
     */
    public function resultado(): BelongsTo
    {
        return $this->belongsTo(Resultado::class);
    }

    /**
     * Relación con usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
