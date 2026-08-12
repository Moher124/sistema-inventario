<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProveedorController extends Controller
{
    public function index(): Response
    {
        $proveedores = Proveedor::orderBy('nombre_proveedor')->get();

        return Inertia::render('proveedores/Index', [
            'proveedores' => $proveedores,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('proveedores/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre_proveedor' => ['required', 'string', 'max:150', 'unique:proveedores,nombre_proveedor'],
            'nit_proveedor' => ['required', 'string', 'max:20', 'unique:proveedores,nit_proveedor'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'direccion' => ['nullable', 'string'],
            'nombre_representante' => ['required', 'string', 'max:100'],
            'activo' => ['boolean'],
        ]);

        Proveedor::create($validated);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Proveedor creado correctamente.',
        ]);

        return to_route('proveedores.index');
    }

    public function edit(Proveedor $proveedor): Response
    {
        return Inertia::render('proveedores/Edit', [
            'proveedor' => $proveedor,
        ]);
    }

    public function update(Request $request, Proveedor $proveedor): RedirectResponse
    {
        $validated = $request->validate([
            'nombre_proveedor' => [
                'required',
                'string',
                'max:150',
                'unique:proveedores,nombre_proveedor,' . $proveedor->id_proveedor . ',id_proveedor',
            ],
            'nit_proveedor' => [
                'required',
                'string',
                'max:20',
                'unique:proveedores,nit_proveedor,' . $proveedor->id_proveedor . ',id_proveedor',
            ],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'direccion' => ['nullable', 'string'],
            'nombre_representante' => ['required', 'string', 'max:100'],
            'activo' => ['boolean'],
        ]);

        $proveedor->update($validated);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Proveedor actualizado correctamente.',
        ]);

        return to_route('proveedores.index');
    }

    public function destroy(Proveedor $proveedor): RedirectResponse
    {
        $proveedor->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Proveedor eliminado correctamente.',
        ]);

        return to_route('proveedores.index');
    }
}
