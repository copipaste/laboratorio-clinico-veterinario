<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaInsumo extends Model
{
    use HasFactory;

    protected $table = 'categorias_insumo';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    /**
     * Relación con insumos
     */
    public function insumos(): HasMany
    {
        return $this->hasMany(Insumo::class, 'categoria_id');
    }
}
