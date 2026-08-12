<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;

#[Fillable(['nombre_usuario', 'password_hash', 'nombre_completo', 'rol', 'activo'])]
#[Hidden(['password_hash'])]
class Usuario extends Authenticatable
{
    protected $table = 'usuarios';

    protected $primaryKey = 'id_usuario';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'creado_el' => 'datetime',
            'password_hash' => 'hashed',
        ];
    }

    /**
     * Le dice a Laravel que la contrasena vive en "password_hash", no en "password".
     */
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }
}
