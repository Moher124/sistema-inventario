<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\JsonResponse;

class CompraController extends Controller
{
    public function index(): Response
    {
        $compras = Compra::with(['producto', 'proveedor'])
            ->orderByDesc('fecha_compra')
            ->orderByDesc('id_compra')
            ->get()
            ->map(fn (Compra $compra) => [
                'id_compra' => $compra->id_compra,
                'producto' => trim($compra->producto->nombre),
                'proveedor' => $compra->proveedor->nombre_proveedor,
                'cantidad' => $compra->cantidad,
                'precio_compra' => $compra->precio_compra,
                'fecha_compra' => $compra->fecha_compra->format('Y-m-d'),
                'fecha_vencimiento' => $compra->fecha_vencimiento?->format('Y-m-d'),
                'anulada' => $compra->anulada,
            ]);

        return Inertia::render('compras/Index', [
            'compras' => $compras,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('compras/Create', [
            'productos' => Producto::orderBy('nombre')
                ->get(['id_producto', 'nombre', 'sku']),
            'proveedores' => Proveedor::where('activo', true)
                ->orderBy('nombre_proveedor')
                ->get(['id_proveedor', 'nombre_proveedor']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_producto' => ['required', 'integer', 'exists:productos,id_producto'],
            'id_proveedor' => ['required', 'integer', 'exists:proveedores,id_proveedor'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'precio_compra' => ['required', 'numeric', 'min:0'],
            'fecha_compra' => ['required', 'date'],
            'fecha_vencimiento' => ['nullable', 'date', 'after_or_equal:fecha_compra'],
        ]);

        Compra::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Compra registrada correctamente.']);

        return to_route('compras.index');
    }

    public function anular(Request $request, Compra $compra): RedirectResponse
    {
        if ($compra->anulada) {
            return to_route('compras.index');
        }

        $validated = $request->validate([
            'motivo_anulacion' => ['nullable', 'string', 'max:255'],
        ]);

        $compra->update([
            'anulada' => true,
            'anulada_el' => now(),
            'motivo_anulacion' => $validated['motivo_anulacion'] ?? null,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Compra anulada correctamente.']);

        return to_route('compras.index');
    }

    public function ultimoPrecio(Request $request): JsonResponse
    {
        $idProducto = $request->query('id_producto');

        $ultimaCompra = Compra::where('id_producto', $idProducto)
            ->where('anulada', false)
            ->orderByDesc('fecha_compra')
            ->orderByDesc('id_compra')
            ->first();

        return response()->json([
            'precio_compra' => $ultimaCompra?->precio_compra,
        ]);
    }
}
