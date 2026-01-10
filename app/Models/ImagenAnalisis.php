<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImagenAnalisis extends Model
{
    use HasFactory;

    protected $table = 'imagenes_analisis';

    protected $fillable = [
        'analisis_id',
        'nombre_archivo',
        'ruta_archivo',
        'descripcion',
        'tipo_imagen',
        'tamanio_kb',
        'subido_por',
        'fecha_subida',
    ];

    protected $casts = [
        'tamanio_kb' => 'integer',
        'fecha_subida' => 'datetime',
    ];

    /**
     * Relación con análisis
     */
    public function analisis(): BelongsTo
    {
        return $this->belongsTo(Analisis::class);
    }

    /**
     * Relación con usuario que subió la imagen
     */
    public function subidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subido_por');
    }
}
