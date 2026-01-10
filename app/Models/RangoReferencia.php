<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RangoReferencia extends Model
{
    use HasFactory;

    protected $table = 'rangos_referencia';

    protected $fillable = [
        'parametro_id',
        'especie_id',
        'valor_minimo',
        'valor_maximo',
        'valor_texto',
    ];

    protected $casts = [
        'valor_minimo' => 'decimal:2',
        'valor_maximo' => 'decimal:2',
    ];

    /**
     * Relación con parámetro de análisis
     */
    public function parametro(): BelongsTo
    {
        return $this->belongsTo(ParametroAnalisis::class, 'parametro_id');
    }

    /**
     * Relación con especie
     */
    public function especie(): BelongsTo
    {
        return $this->belongsTo(Especie::class);
    }
}
