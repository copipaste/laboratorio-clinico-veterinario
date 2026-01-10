<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Veterinaria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'responsable',
        'telefono',
        'email',
        'direccion',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    /**
     * Relación con muestras
     */
    public function muestras(): HasMany
    {
        return $this->hasMany(Muestra::class);
    }
}
