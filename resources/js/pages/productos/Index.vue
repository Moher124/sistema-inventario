<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

type Producto = {
    id_producto: number;
    sku: string;
    nombre: string;
    precio_venta: string;
    disponible: boolean;
    tipo_producto: string | null;
    presentacion: string | null;
    stock: number;
};

defineProps<{
    productos: Producto[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Productos', href: '/productos' },
        ],
    },
});

function eliminar(producto: Producto) {
    if (!confirm(`¿Eliminar el producto "${producto.nombre}"? Esta acción no se puede deshacer.`)) {
        return;
    }

    router.delete(`/productos/${producto.id_producto}`);
}
</script>

<template>
    <Head title="Productos" />

    <div class="flex flex-col gap-4 p-4">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold">Productos</h1>
            <Button as-child>
                <Link href="/productos/crear">Nuevo producto</Link>
            </Button>
        </div>

        <div class="overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <table class="w-full text-sm">
                <thead class="bg-muted/50 text-left">
                    <tr>
                        <th class="p-3">SKU</th>
                        <th class="p-3">Nombre</th>
                        <th class="p-3">Tipo</th>
                        <th class="p-3">Presentación</th>
                        <th class="p-3 text-right">Precio</th>
                        <th class="p-3">Disponible</th>
                        <th class="p-3 text-right">Stock</th>
                        <th class="p-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="producto in productos"
                        :key="producto.id_producto"
                        class="border-t border-sidebar-border/70 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-mono text-xs">{{ producto.sku }}</td>
                        <td class="p-3">{{ producto.nombre }}</td>
                        <td class="p-3">{{ producto.tipo_producto ?? '—' }}</td>
                        <td class="p-3">{{ producto.presentacion ?? '—' }}</td>
                        <td class="p-3 text-right">Q{{ producto.precio_venta }}</td>
                        <td class="p-3">
                            <span
                                :class="producto.disponible ? 'text-green-600' : 'text-red-600'"
                            >
                                {{ producto.disponible ? 'Sí' : 'No' }}
                            </span>
                        </td>
                        <td class="p-3 text-right font-medium">
                            <span :class="producto.stock <= 0 ? 'text-red-600' : ''">
                                {{ producto.stock }}
                            </span>
                        </td>
                        <td class="p-3 text-right">
                            <div class="flex justify-end gap-2">
                                <Link
                                    :href="`/productos/${producto.id_producto}/editar`"
                                    class="text-sm text-blue-600 hover:underline"
                                >
                                    Editar
                                </Link>
                                <button
                                    type="button"
                                    class="text-sm text-red-600 hover:underline"
                                    @click="eliminar(producto)"
                                >
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="productos.length === 0">
                        <td colspan="7" class="p-3 text-center text-muted-foreground">
                            No hay productos cargados todavía.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
