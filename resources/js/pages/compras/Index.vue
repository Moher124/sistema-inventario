<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

type Compra = {
    id_compra: number;
    producto: string;
    proveedor: string;
    cantidad: number;
    precio_compra: string;
    fecha_compra: string;
    fecha_vencimiento: string | null;
    anulada: boolean;
};

defineProps<{
    compras: Compra[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Compras', href: '/compras' }],
    },
});

function anular(compra: Compra) {
    const motivo = prompt('Motivo de la anulación (opcional):');

    if (motivo === null) return; // canceló el prompt

    if (!confirm(`¿Anular la compra de "${compra.producto}"? Esta acción no se puede deshacer.`)) {
        return;
    }

    router.patch(`/compras/${compra.id_compra}/anular`, {
        motivo_anulacion: motivo || null,
    });
}
</script>

<template>
    <Head title="Compras" />

    <div class="flex flex-col gap-4 p-4">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold">Compras</h1>
            <Button as-child>
                <Link href="/compras/crear">Registrar compra</Link>
            </Button>
        </div>

        <div class="overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <table class="w-full text-sm">
                <thead class="bg-muted/50 text-left">
                    <tr>
                        <th class="p-3">Fecha</th>
                        <th class="p-3">Producto</th>
                        <th class="p-3">Proveedor</th>
                        <th class="p-3 text-right">Cantidad</th>
                        <th class="p-3 text-right">Precio unitario</th>
                        <th class="p-3 text-right">Total</th>
                        <th class="p-3">Vencimiento</th>
                        <th class="p-3">Estado</th>
                        <th class="p-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="compra in compras"
                        :key="compra.id_compra"
                        class="border-t border-sidebar-border/70 dark:border-sidebar-border"
                        :class="compra.anulada ? 'opacity-50' : ''"
                    >
                        <td class="p-3">{{ compra.fecha_compra }}</td>
                        <td class="p-3">{{ compra.producto }}</td>
                        <td class="p-3">{{ compra.proveedor }}</td>
                        <td class="p-3 text-right">{{ compra.cantidad }}</td>
                        <td class="p-3 text-right">Q{{ compra.precio_compra }}</td>
                        <td class="p-3 text-right font-medium">
                            Q{{ (Number(compra.cantidad) * Number(compra.precio_compra)).toFixed(2) }}
                        </td>
                        <td class="p-3">{{ compra.fecha_vencimiento ?? '—' }}</td>
                        <td class="p-3">
                            <span
                                class="rounded-full px-2 py-0.5 text-xs font-medium"
                                :class="compra.anulada
                                    ? 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-400'
                                    : 'bg-green-100 text-green-700 dark:bg-green-950 dark:text-green-400'"
                            >
                                {{ compra.anulada ? 'Anulada' : 'Activa' }}
                            </span>
                        </td>
                        <td class="p-3 text-right">
                            <button
                                v-if="!compra.anulada"
                                type="button"
                                class="text-sm text-red-600 hover:underline"
                                @click="anular(compra)"
                            >
                                Anular
                            </button>
                        </td>
                    </tr>
                    <tr v-if="compras.length === 0">
                        <td colspan="9" class="p-3 text-center text-muted-foreground">
                            No hay compras registradas todavía.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
