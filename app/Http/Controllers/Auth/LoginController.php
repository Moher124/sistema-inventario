<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('auth/Login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'nombre_usuario' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $usuario = Usuario::where('nombre_usuario', $credentials['nombre_usuario'])
            ->where('activo', true)
            ->first();

        if (! $usuario || ! Auth::guard('web')->attempt([
            'nombre_usuario' => $credentials['nombre_usuario'],
            'password' => $credentials['password'],
        ])) {
            throw ValidationException::withMessages([
                'nombre_usuario' => 'Las credenciales no son correctas.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
