<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $table = 'productos';

    protected $primaryKey = 'id_producto';

    public $timestamps = false;

    protected $fillable = ['sku', 'nombre', 'precio_venta', 'disponible'];

    protected function casts(): array
    {
        return [
            'disponible' => 'boolean',
            'precio_venta' => 'decimal:2',
            'agregado_el' => 'datetime',
        ];
    }

    public function detalle(): HasOne
    {
        return $this->hasOne(DetalleProducto::class, 'id_producto', 'id_producto');
    }

    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class, 'id_producto', 'id_producto');
    }

    public function ventaDetalles(): HasMany
    {
        return $this->hasMany(VentaDetalle::class, 'id_producto', 'id_producto');
    }
}
