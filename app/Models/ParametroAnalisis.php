<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParametroAnalisis extends Model
{
    use HasFactory;

    protected $table = 'parametros_analisis';

    protected $fillable = [
        'tipo_analisis_id',
        'nombre',
        'unidad',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];

    /**
     * Relación con tipo de análisis
     */
    public function tipoAnalisis(): BelongsTo
    {
        return $this->belongsTo(TipoAnalisis::class);
    }

    /**
     * Relación con rangos de referencia
     */
    public function rangosReferencia(): HasMany
    {
        return $this->hasMany(RangoReferencia::class, 'parametro_id');
    }

    /**
     * Relación con resultados
     */
    public function resultados(): HasMany
    {
        return $this->hasMany(Resultado::class, 'parametro_id');
    }
}
