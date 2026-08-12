<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Compra extends Model
{
    protected $table = 'compras';

    protected $primaryKey = 'id_compra';

    public $timestamps = false;

    protected $fillable = [
        'id_producto',
        'id_proveedor',
        'precio_compra',
        'cantidad',
        'fecha_compra',
        'fecha_vencimiento',
        'anulada',
        'anulada_el',
        'motivo_anulacion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_compra' => 'date',
            'fecha_vencimiento' => 'date',
            'anulada' => 'boolean',
            'anulada_el' => 'datetime',
        ];
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }
}
