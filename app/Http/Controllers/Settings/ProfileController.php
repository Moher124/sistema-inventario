<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/Profile');
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre_completo' => ['required', 'string', 'max:150'],
        ]);

        $request->user()->update($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Perfil actualizado.']);

        return to_route('profile.edit');
    }
}
