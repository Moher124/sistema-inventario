<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class VentaController extends Controller
{
    public function index(): Response
    {
        $ventas = Venta::with(['detalles.producto', 'cliente'])
            ->orderByDesc('fecha_venta')
            ->orderByDesc('id_venta')
            ->get()
            ->map(fn (Venta $venta) => [
                'id_venta' => $venta->id_venta,
                'fecha_venta' => $venta->fecha_venta->format('Y-m-d'),
                'total' => $venta->total,
                'observacion' => $venta->observacion,
                'tipo_cliente' => $venta->tipo_cliente,
                'cliente' => $venta->cliente?->nombre_cliente,
                'cantidad_items' => $venta->detalles->sum('cantidad_vendida'),
                'nit_cliente' => $venta->cliente?->nit_cliente,
                'anulada' => $venta->anulada,
                'productos' => $venta->detalles->map(fn (VentaDetalle $detalle) => [
                    'nombre' => $detalle->producto?->nombre,
                    'cantidad_vendida' => $detalle->cantidad_vendida,
                    'anulada' => $venta->anulada,
                ]),
            ]);

        return Inertia::render('ventas/Index', [
            'ventas' => $ventas,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('ventas/Create', [
            'productos' => $this->productosDisponibles(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validarVenta($request);

        $total = $this->calcularTotal($validated['detalles']);

        DB::transaction(function () use ($validated, $total) {
        $idCliente = $this->resolverCliente($validated);

        $venta = Venta::create([
            'fecha_venta' => $validated['fecha_venta'],
            'total' => $total,
            'observacion' => $validated['observacion'] ?? null,
            'tipo_cliente' => $validated['tipo_cliente'],
            'id_cliente' => $idCliente,
        ]);

        $this->guardarDetalles($venta, $validated['detalles']);
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Venta registrada correctamente.']);

        return to_route('ventas.index');
    }


    public function anular(Request $request, Venta $venta): RedirectResponse
    {
        if ($venta->anulada) {
            return to_route('ventas.index');
        }

        $validated = $request->validate([
            'motivo_anulacion' => ['nullable', 'string', 'max:255'],
        ]);

        $venta->update([
            'anulada' => true,
            'anulada_el' => now(),
            'motivo_anulacion' => $validated['motivo_anulacion'] ?? null,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Venta anulada correctamente.']);

        return to_route('ventas.index');
    }

   private function validarVenta(Request $request): array
    {
        return $request->validate([
            'fecha_venta' => ['required', 'date'],
            'observacion' => ['nullable', 'string', 'max:255'],
            'tipo_cliente' => ['required', 'in:CF,NIT'],
            'nit_cliente' => ['required_if:tipo_cliente,NIT', 'nullable', 'string', 'max:20'],
            'nombre_cliente' => ['required_if:tipo_cliente,NIT', 'nullable', 'string', 'max:150'],
            'telefono_cliente' => ['nullable', 'string', 'max:20'],
            'email_cliente' => ['nullable', 'email', 'max:100'],
            'direccion_cliente' => ['nullable', 'string'],
            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.id_producto' => ['required', 'integer', 'exists:productos,id_producto'],
            'detalles.*.cantidad_vendida' => ['required', 'integer', 'min:1'],
            'detalles.*.precio_unitario' => ['required', 'numeric', 'min:0'],
        ]);
    }

    private function calcularTotal(array $detalles): float
    {
        return collect($detalles)
            ->sum(fn (array $detalle) => $detalle['cantidad_vendida'] * $detalle['precio_unitario']);
    }

    private function guardarDetalles(Venta $venta, array $detalles): void
    {
        foreach ($detalles as $detalle) {
            VentaDetalle::create([
                'id_venta' => $venta->id_venta,
                'id_producto' => $detalle['id_producto'],
                'cantidad_vendida' => $detalle['cantidad_vendida'],
                'precio_unitario' => $detalle['precio_unitario'],
            ]);
        }
    }

    private function productosDisponibles()
    {
        return Producto::where('disponible', true)
            ->orderBy('nombre')
            ->get(['id_producto', 'nombre', 'precio_venta'])
            ->map(fn (Producto $producto) => [
                'id_producto' => $producto->id_producto,
                'nombre' => $producto->nombre,
                'precio_venta' => $producto->precio_venta,
            ]);
    }

    private function resolverCliente(array $validated): ?int
    {
        if ($validated['tipo_cliente'] !== 'NIT') {
            return null;
        }

        $cliente = Cliente::firstOrCreate(
            ['nit_cliente' => $validated['nit_cliente']],
            [
                'nombre_cliente' => $validated['nombre_cliente'],
                'telefono_cliente' => $validated['telefono_cliente'] ?? null,
                'email_cliente' => $validated['email_cliente'] ?? null,
                'direccion_cliente' => $validated['direccion_cliente'] ?? null,
                'activo' => true,
            ],
        );

        return $cliente->id_cliente;
    }
}
