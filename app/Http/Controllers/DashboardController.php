<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        Carbon::setLocale('es');

        $mes = $request->query('mes')
            ? Carbon::createFromFormat('Y-m', $request->query('mes'))->startOfMonth()
            : now()->startOfMonth();

        $inicioMes = $mes->copy()->startOfMonth();
        $finMes = $mes->copy()->endOfMonth();

        // 1. Gasto Mensual
        $gastoMensual = Compra::where('anulada', false)
            ->whereBetween('fecha_compra', [$inicioMes, $finMes])
            ->selectRaw('COALESCE(SUM(cantidad * precio_compra), 0) as total')
            ->value('total');

        // 2. Métricas de Ventas del Mes (Agrupadas en una sola consulta para optimizar)
        $metricasVentas = Venta::where('anulada', false)
            ->whereBetween('fecha_venta', [$inicioMes, $finMes])
            ->selectRaw('
                COALESCE(SUM(total), 0) as ingreso_total,
                COUNT(id_venta) as total_transacciones,
                COALESCE(AVG(total), 0) as ticket_medio
            ')
            ->first();

        // 3. Productos con Stock Bajo
        $productosStockBajo = Producto::with('detalle')
            ->withSum(['compras as total_comprado' => fn ($q) => $q->where('anulada', false)], 'cantidad')
            ->withSum(['ventaDetalles as total_vendido' => fn ($q) => $q->whereRelation('venta', 'anulada', false)], 'cantidad_vendida')
            ->get()
            ->map(fn (Producto $producto) => [
                'nombre' => $producto->nombre,
                'stock' => (int) ($producto->total_comprado ?? 0) - (int) ($producto->total_vendido ?? 0),
            ])
            ->filter(fn (array $p) => $p['stock'] <= 5)
            ->sortBy('stock')
            ->values();


        $topProductos = VentaDetalle::whereRelation('venta', 'anulada', false)
        ->whereRelation('venta', 'fecha_venta', '>=', $inicioMes)
        ->whereRelation('venta', 'fecha_venta', '<=', $finMes)
        ->with('producto:id_producto,nombre')
        ->select('id_producto', DB::raw('SUM(cantidad_vendida * precio_unitario) as total_recaudado'))
        ->groupBy('id_producto')
        ->orderByDesc('total_recaudado')
        ->limit(5)
        ->get()
        ->map(fn ($detalle) => [
            'nombre' => $detalle->producto?->nombre ?? 'Producto Eliminado',
            'total' => (float) $detalle->total_recaudado, //Envia el total monetario
        ]);

        // 5. NUEVO: Datos Estructurados para Gráfica (Ventas por Día del Mes actual)
        // Esto facilitará enormemente pintar una gráfica de líneas o barras en el frontend
        $ventasPorDia = Venta::where('anulada', false)
            ->whereBetween('fecha_venta', [$inicioMes, $finMes])
            ->selectRaw('DATE(fecha_venta) as fecha, SUM(total) as total')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get()
            ->map(fn ($v) => [
                'fecha' => Carbon::parse($v->fecha)->format('d/m'),
                'total' => (float) $v->total
            ]);

        return Inertia::render('Dashboard', [
            'mes' => $mes->format('Y-m'),
            'mesLabel' => $mes->translatedFormat('F Y'),
            'totalGastado' => (float) $gastoMensual,
            'gananciaBruta' => (float) $metricasVentas->ingreso_total,
            'gananciaReal' => (float) $metricasVentas->ingreso_total - (float) $gastoMensual,
            'ticketMedio' => (float) $metricasVentas->ticket_medio,
            'totalVentasCount' => (int) $metricasVentas->total_transacciones,
            'productosStockBajo' => $productosStockBajo,
            'topProductos' => $topProductos,
            'datosGraficaVentas' => $ventasPorDia, // librería de graficos
        ]);
    }
}
