<?php

namespace App\Http\Controllers;

use App\Models\DetalleProducto;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\JsonResponse;

class ProductoController extends Controller
{
    public function index(): Response
    {
        $productos = Producto::with('detalle')
            ->withSum(['compras as total_comprado' => fn ($query) => $query->where('anulada', false)], 'cantidad')
            ->withSum(['ventaDetalles as total_vendido' => fn ($query) => $query->whereRelation('venta', 'anulada', false)], 'cantidad_vendida')
            ->orderBy('nombre')
            ->get()
            ->map(fn (Producto $producto) => [
                'id_producto' => $producto->id_producto,
                'sku' => trim($producto->sku),
                'nombre' => $producto->nombre,
                'precio_venta' => $producto->precio_venta,
                'disponible' => $producto->disponible,
                'tipo_producto' => $producto->detalle?->tipo_producto,
                'presentacion' => $producto->detalle?->presentacion,
                'stock' => (int) ($producto->total_comprado ?? 0) - (int) ($producto->total_vendido ?? 0),
            ]);

        return Inertia::render('productos/Index', [
            'productos' => $productos,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('productos/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:13', 'unique:productos,sku'],
            'nombre' => ['required', 'string', 'max:150'],
            'precio_venta' => ['required', 'numeric', 'min:0'],
            'disponible' => ['boolean'],
            'tipo_producto' => ['required', 'string', 'max:100'],
            'presentacion' => ['required', 'string', 'max:50'],
            'unidades_por_empaque' => ['required', 'integer', 'min:1'],
            'descripcion' => ['nullable', 'string'],
            'ingredientes' => ['nullable', 'string'],
        ]);


        // todo lo que esta adentro de esta funcion se trata como una sola unidad
        // o se guardan las dos cosas (producto + detalle)
        DB::transaction(function () use ($validated) {
            $producto = Producto::create([
                'sku' => $validated['sku'],
                'nombre' => $validated['nombre'],
                'precio_venta' => $validated['precio_venta'],
                'disponible' => $validated['disponible'] ?? true,
            ]);

            DetalleProducto::create([
                'id_producto' => $producto->id_producto,
                'tipo_producto' => $validated['tipo_producto'],
                'presentacion' => $validated['presentacion'],
                'unidades_por_empaque' => $validated['unidades_por_empaque'],
                'descripcion' => $validated['descripcion'] ?? null,
                'ingredientes' => $validated['ingredientes'] ?? null,
            ]);
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Producto creado correctamente.']);

        return to_route('productos.index');
    }

    public function sugerirSku(Request $request): JsonResponse
    {
        $tipoProducto = $request->query('tipo_producto', '');

        $letras = strtoupper(preg_replace('/[^a-zA-Z]/', '', $tipoProducto));
        $prefijo = str_pad(substr($letras, 0, 3), 3, 'X');

        $ultimo = Producto::where('sku', 'like', $prefijo.'%')
            ->orderByDesc('sku')
            ->first();

        $siguienteNumero = $ultimo
            ? ((int) substr(trim($ultimo->sku), 3)) + 1
            : 1;

        $sku = $prefijo.str_pad((string) $siguienteNumero, 10, '0', STR_PAD_LEFT);

        return response()->json(['sku' => $sku]);
    }

    public function edit(Producto $producto): Response
    {
        $producto->load('detalle');

        return Inertia::render('productos/Edit', [
            'producto' => [
                'id_producto' => $producto->id_producto,
                'sku' => trim($producto->sku),
                'nombre' => $producto->nombre,
                'precio_venta' => $producto->precio_venta,
                'disponible' => $producto->disponible,
                'tipo_producto' => $producto->detalle?->tipo_producto,
                'presentacion' => $producto->detalle?->presentacion,
                'unidades_por_empaque' => $producto->detalle?->unidades_por_empaque,
                'descripcion' => $producto->detalle?->descripcion,
                'ingredientes' => $producto->detalle?->ingredientes,
            ],
        ]);
    }

    public function update(Request $request, Producto $producto): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:13', 'unique:productos,sku,'.$producto->id_producto.',id_producto'],
            'nombre' => ['required', 'string', 'max:150'],
            'precio_venta' => ['required', 'numeric', 'min:0'],
            'disponible' => ['boolean'],
            'tipo_producto' => ['required', 'string', 'max:100'],
            'presentacion' => ['required', 'string', 'max:50'],
            'unidades_por_empaque' => ['required', 'integer', 'min:1'],
            'descripcion' => ['nullable', 'string'],
            'ingredientes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated, $producto) {
            $producto->update([
                'sku' => $validated['sku'],
                'nombre' => $validated['nombre'],
                'precio_venta' => $validated['precio_venta'],
                'disponible' => $validated['disponible'] ?? true,
            ]);

            $producto->detalle()->updateOrCreate(
                ['id_producto' => $producto->id_producto],
                [
                    'tipo_producto' => $validated['tipo_producto'],
                    'presentacion' => $validated['presentacion'],
                    'unidades_por_empaque' => $validated['unidades_por_empaque'],
                    'descripcion' => $validated['descripcion'] ?? null,
                    'ingredientes' => $validated['ingredientes'] ?? null,
                ],
            );
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Producto actualizado correctamente.']);

        return to_route('productos.index');
    }

    public function destroy(Producto $producto): RedirectResponse
    {
        $producto->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Producto eliminado.']);

        return to_route('productos.index');
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
}
