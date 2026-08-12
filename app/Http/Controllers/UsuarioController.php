<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UsuarioController extends Controller
{
    public function index(): Response
    {
        $usuarios = Usuario::orderBy('nombre_completo')->get([
            'id_usuario', 'nombre_usuario', 'nombre_completo', 'rol', 'activo',
        ]);

        return Inertia::render('usuarios/Index', [
            'usuarios' => $usuarios,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('usuarios/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre_usuario' => ['required', 'string', 'max:50', 'unique:usuarios,nombre_usuario'],
            'nombre_completo' => ['required', 'string', 'max:150'],
            'password' => ['required', 'string', 'min:8'],
            'rol' => ['required', 'in:admin,empleado'],
            'activo' => ['boolean'],
        ]);

        Usuario::create([
            'nombre_usuario' => $validated['nombre_usuario'],
            'nombre_completo' => $validated['nombre_completo'],
            'password_hash' => $validated['password'],
            'rol' => $validated['rol'],
            'activo' => $validated['activo'] ?? true,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Usuario creado correctamente.']);

        return to_route('usuarios.index');
    }

    public function edit(Usuario $usuario): Response
    {
        return Inertia::render('usuarios/Edit', [
            'usuario' => $usuario->only([
                'id_usuario', 'nombre_usuario', 'nombre_completo', 'rol', 'activo',
            ]),
        ]);
    }

    public function update(Request $request, Usuario $usuario): RedirectResponse
    {
        $validated = $request->validate([
            'nombre_usuario' => ['required', 'string', 'max:50', 'unique:usuarios,nombre_usuario,'.$usuario->id_usuario.',id_usuario'],
            'nombre_completo' => ['required', 'string', 'max:150'],
            'password' => ['nullable', 'string', 'min:8'],
            'rol' => ['required', 'in:admin,empleado'],
            'activo' => ['boolean'],
        ]);

        $datos = [
            'nombre_usuario' => $validated['nombre_usuario'],
            'nombre_completo' => $validated['nombre_completo'],
            'rol' => $validated['rol'],
            'activo' => $validated['activo'] ?? true,
        ];

        if (! empty($validated['password'])) {
            $datos['password_hash'] = $validated['password'];
        }

        $usuario->update($datos);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Usuario actualizado correctamente.']);

        return to_route('usuarios.index');
    }

    public function destroy(Request $request, Usuario $usuario): RedirectResponse
    {
        if ($usuario->id_usuario === $request->user()->id_usuario) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'No podés desactivar tu propio usuario.']);

            return to_route('usuarios.index');
        }

        $usuario->update(['activo' => false]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Usuario desactivado.']);

        return to_route('usuarios.index');
    }
}
