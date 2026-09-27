<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, TrendingUp, AlertTriangle, Award } from '@lucide/vue';
import { computed } from 'vue';
import apexchart from 'vue3-apexcharts';

type ProductoStockBajo = {
    nombre: string;
    stock: number;
};

type TopProducto = {
    nombre: string;
    total: number;
};

type DatoGrafica = {
    fecha: string;
    total: number;
};

const props = defineProps<{
    mes: string; // formato "2026-08"
    mesLabel: string; // ej. "agosto 2026"
    totalGastado: number;
    gananciaBruta: number;
    gananciaReal: number;
    ticketMedio: number;
    totalVentasCount: number;
    productosStockBajo: ProductoStockBajo[];
    topProductos: TopProducto[];
    datosGraficaVentas: DatoGrafica[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }],
    },
});

function mesAdyacente(offset: number): string {
    const [anio, mes] = props.mes.split('-').map(Number);
    const fecha = new Date(anio, mes - 1 + offset, 1);
    const anioNuevo = fecha.getFullYear();
    const mesNuevo = String(fecha.getMonth() + 1).padStart(2, '0');
    return `${anioNuevo}-${mesNuevo}`;
}

function formatQ(valor: number): string {
    return valor.toLocaleString('es-GT', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

const chartOptions = computed(() => ({
    chart: {
        id: 'ventas-diarias',
        type: 'area',
        toolbar: { show: false },
        fontFamily: 'inherit',
        background: 'transparent',
    },
    colors: ['#10b981'], // Verde esmeralda
    stroke: { curve: 'smooth', width: 2 },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.45,
            opacityTo: 0.05,
            stops: [0, 90, 100]
        }
    },
    labels: (props.datosGraficaVentas || []).map(d => d.fecha),
    xaxis: {
        type: 'category',
        axisBorder: { show: false },
        axisTicks: { show: false },
        labels: { style: { colors: '#6b7280' } }
    },
    yaxis: {
        labels: {
            style: { colors: '#6b7280' },
            formatter: (val: number) => `Q${val.toFixed(0)}`
        }
    },
    grid: { borderColor: '#e5e7eb20', strokeDashArray: 4 },
    theme: { mode: 'dark' },
    tooltip: { y: { formatter: (val: number) => `Q${formatQ(val)}` } }
} as any));

const chartSeries = computed(() => [{
    name: 'Ventas del día',
    data: (props.datosGraficaVentas || []).map(d => d.total)
}]);
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4">
        <!-- Encabezado y Selector de Mes -->
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold">Dashboard</h1>

            <div class="flex items-center gap-2">
                <Link
                    :href="`/dashboard?mes=${mesAdyacente(-1)}`"
                    class="flex size-8 items-center justify-center rounded-md border border-sidebar-border/70 hover:bg-muted dark:border-sidebar-border"
                >
                    <ChevronLeft class="size-4" />
                </Link>
                <span class="min-w-32 text-center text-sm font-medium capitalize">{{ mesLabel }}</span>
                <Link
                    :href="`/dashboard?mes=${mesAdyacente(1)}`"
                    class="flex size-8 items-center justify-center rounded-md border border-sidebar-border/70 hover:bg-muted dark:border-sidebar-border"
                >
                    <ChevronRight class="size-4" />
                </Link>
            </div>
        </div>

        <!-- Módulos de Tarjetas Principales (KPIs) -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Total gastado</p>
                <p class="mt-1 text-2xl font-semibold text-red-600">Q{{ formatQ(totalGastado) }}</p>
            </div>

            <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Ganancia bruta</p>
                <p class="mt-1 text-2xl font-semibold">Q{{ formatQ(gananciaBruta) }}</p>
            </div>

            <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Ganancia real</p>
                <p
                    class="mt-1 text-2xl font-semibold"
                    :class="gananciaReal >= 0 ? 'text-green-600' : 'text-red-600'"
                >
                    Q{{ formatQ(gananciaReal) }}
                </p>
            </div>

            <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Ticket Medio</p>
                <p class="mt-1 text-2xl font-semibold text-blue-500">Q{{ formatQ(ticketMedio) }}</p>
            </div>

            <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Ventas Realizadas</p>
                <p class="mt-1 text-2xl font-semibold text-purple-500">{{ totalVentasCount }} transac.</p>
            </div>
        </div>


        <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
            <div class="mb-4 flex items-center gap-2">
                <TrendingUp class="size-4 text-emerald-500" />
                <h2 class="text-sm font-medium">Flujo de Ventas Diarias</h2>
            </div>
            <div v-if="datosGraficaVentas.length > 0" class="h-72 w-full">
                <apexchart type="area" height="100%" :options="chartOptions" :series="chartSeries" />
            </div>
            <div v-else class="flex h-72 items-center justify-center text-sm text-muted-foreground">
                No hay transacciones registradas en este mes para graficar.
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">

            <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <div class="flex items-center gap-2 border-b border-sidebar-border/70 p-4 dark:border-sidebar-border">
                    <AlertTriangle class="size-4 text-amber-500" />
                    <h2 class="text-sm font-medium">Productos con stock bajo (≤ 5 unidades)</h2>
                </div>

                <table class="w-full text-sm">
                    <tbody>
                        <tr
                            v-for="producto in productosStockBajo"
                            :key="producto.nombre"
                            class="border-t border-sidebar-border/70 first:border-t-0 dark:border-sidebar-border"
                        >
                            <td class="p-3">{{ producto.nombre }}</td>
                            <td class="p-3 text-right">
                                <span :class="producto.stock <= 0 ? 'font-medium text-red-600' : 'text-amber-600'">
                                    {{ producto.stock }} unidades
                                </span>
                            </td>
                        </tr>
                        <tr v-if="productosStockBajo.length === 0">
                            <td class="p-3 text-center text-muted-foreground">
                                Todos los productos tienen stock saludable.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>


            <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <div class="flex items-center gap-2 border-b border-sidebar-border/70 p-4 dark:border-sidebar-border">
                    <Award class="size-4 text-blue-500" />
                    <h2 class="text-sm font-medium">Top 5 Productos Más Vendidos del Mes</h2>
                </div>

                <table class="w-full text-sm">
                    <tbody>
                        <tr
                            v-for="(producto, idx) in topProductos"
                            :key="producto.nombre"
                            class="border-t border-sidebar-border/70 first:border-t-0 dark:border-sidebar-border"
                        >
                            <td class="p-3 font-medium text-muted-foreground w-10">#{{ idx + 1 }}</td>
                            <td class="p-3">{{ producto.nombre }}</td>
                            <td class="p-3 text-right font-semibold text-emerald-600">
                                Q{{ formatQ(producto.total) }}
                            </td>
                        </tr>
                        <tr v-if="topProductos.length === 0">
                            <td class="p-3 text-center text-muted-foreground">
                                No hay datos de ventas en este periodo.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
