<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleProducto extends Model
{
    protected $table = 'detalle_producto';

    protected $primaryKey = 'id_producto';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'id_producto',
        'descripcion',
        'ingredientes',
        'tipo_producto',
        'presentacion',
        'unidades_por_empaque',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}
