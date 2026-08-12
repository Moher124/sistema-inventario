<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $primaryKey = 'id_cliente';

    public $timestamps = false;

    protected $fillable = [
        'nit_cliente',
        'nombre_cliente',
        'telefono_cliente',
        'email_cliente',
        'direccion_cliente',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'fecha_registro' => 'datetime',
        ];
    }
}
