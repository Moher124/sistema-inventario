<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

type Venta = {
    id_venta: number;
    fecha_venta: string;
    total: string;
    observacion: string | null;
    tipo_cliente: 'CF' | 'NIT';
    cliente: string | null;
    nit_cliente: string | null;
    cantidad_items: number;
    productos: {
        nombre: string | null;
        cantidad_vendida: number;
    }[];
    anulada: boolean;
};

defineProps<{
    ventas: Venta[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Ventas', href: '/ventas' }],
    },
});

function anular(venta: Venta) {
    const motivo = prompt('Motivo de la anulación (opcional):');

    if (motivo === null) return;

    if (!confirm(`¿Anular la venta del ${venta.fecha_venta}? Esta acción no se puede deshacer.`)) {
        return;
    }

    router.patch(`/ventas/${venta.id_venta}/anular`, {
        motivo_anulacion: motivo || null,
    });
}

function clienteDisplay(venta: Venta) {
    return venta.tipo_cliente === 'NIT' ? (venta.cliente ?? '—') : 'Consumidor Final';
}

function nitDisplay(venta: Venta) {
    return venta.tipo_cliente === 'NIT' ? (venta.nit_cliente ?? '—') : '—';
}

function productosResumen(venta: Venta) {
    return venta.productos.map((p) => `${p.nombre} (${p.cantidad_vendida})`).join(', ');
}

function productosVisibles(venta: Venta) {
    return venta.productos.slice(0, 2);
}

function productosOcultos(venta: Venta) {
    return Math.max(venta.productos.length - 2, 0);
}
</script>

<template>
    <Head title="Ventas" />

    <div class="flex flex-col gap-4 p-4">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold">Ventas</h1>
            <Button as-child>
                <Link href="/ventas/crear">Nueva venta</Link>
            </Button>
        </div>

        <div class="overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <table class="w-full table-fixed text-sm">
                <colgroup>
                    <col class="w-[9%]">
                    <col class="w-[14%]">
                    <col class="w-[11%]">
                    <col class="w-[26%]">
                    <col class="w-[7%]">
                    <col class="w-[11%]">
                    <col class="w-[10%]">
                    <col class="w-[12%]">
                </colgroup>
                <thead class="bg-muted/50 text-left">
                    <tr>
                        <th class="p-3">Fecha</th>
                        <th class="p-3">Cliente</th>
                        <th class="p-3">NIT</th>
                        <th class="p-3">Productos</th>
                        <th class="p-3 text-right">Ítems</th>
                        <th class="p-3 text-right">Total</th>
                        <th class="p-3">Estado</th>
                        <th class="p-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="venta in ventas"
                        :key="venta.id_venta"
                        class="border-t border-sidebar-border/70 dark:border-sidebar-border"
                        :class="venta.anulada ? 'opacity-50' : ''"
                    >
                        <td class="p-3 truncate">{{ venta.fecha_venta }}</td>
                        <td class="p-3 truncate" :title="clienteDisplay(venta)">
                            {{ clienteDisplay(venta) }}
                        </td>
                        <td class="p-3 truncate text-muted-foreground" :title="nitDisplay(venta)">
                            {{ nitDisplay(venta) }}
                        </td>
                        <td class="p-3">
                            <div class="truncate text-muted-foreground" :title="productosResumen(venta)">
                                <span v-for="(p, index) in productosVisibles(venta)" :key="index">
                                    {{ p.nombre }} ({{ p.cantidad_vendida }})<span v-if="index < productosVisibles(venta).length - 1">, </span>
                                </span>
                                <span v-if="productosOcultos(venta) > 0" class="text-xs">
                                    +{{ productosOcultos(venta) }} más
                                </span>
                            </div>
                        </td>
                        <td class="p-3 text-right">{{ venta.cantidad_items }}</td>
                        <td class="p-3 text-right font-medium">Q{{ venta.total }}</td>
                        <td class="p-3">
                            <span
                                class="rounded-full px-2 py-0.5 text-xs font-medium"
                                :class="venta.anulada
                                    ? 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-400'
                                    : 'bg-green-100 text-green-700 dark:bg-green-950 dark:text-green-400'"
                            >
                                {{ venta.anulada ? 'Anulada' : 'Activa' }}
                            </span>
                        </td>
                        <td class="p-3 text-right">
                            <button
                                v-if="!venta.anulada"
                                type="button"
                                class="text-sm text-red-600 hover:underline"
                                @click="anular(venta)"
                            >
                                Anular
                            </button>
                        </td>
                    </tr>
                    <tr v-if="ventas.length === 0">
                        <td colspan="8" class="p-3 text-center text-muted-foreground">
                            No hay ventas registradas todavía.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
