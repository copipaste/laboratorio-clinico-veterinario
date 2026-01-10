<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TokenDescarga extends Model
{
    use HasFactory;

    protected $table = 'tokens_descarga';

    protected $fillable = [
        'pdf_id',
        'token',
        'fecha_expiracion',
        'usado',
    ];

    protected $casts = [
        'fecha_expiracion' => 'datetime',
        'usado' => 'boolean',
    ];

    /**
     * Relación con PDF
     */
    public function pdf(): BelongsTo
    {
        return $this->belongsTo(Pdf::class);
    }

    /**
     * Relación con logs de descarga
     */
    public function logsDescarga(): HasMany
    {
        return $this->hasMany(LogDescarga::class, 'token_id');
    }
}
