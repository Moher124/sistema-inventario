<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Venta extends Model
{
    protected $primaryKey = 'id_venta';

    // La tabla no tiene created_at / updated_at
    public $timestamps = false;

    protected $fillable = [
        'fecha_venta',
        'total',
        'observacion',
        'id_cliente',
        'tipo_cliente',
        'anulada',
        'anulada_el',
        'motivo_anulacion',
    ];

    protected $casts = [
        'fecha_venta' => 'date',
        'total' => 'decimal:2',
        'anulada' => 'boolean',
        'anulada_el' => 'datetime',
    ];

    public function detalles(): HasMany
    {
        return $this->hasMany(VentaDetalle::class, 'id_venta', 'id_venta');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }
}
