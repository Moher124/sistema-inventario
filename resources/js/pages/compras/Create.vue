<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { computed } from 'vue';
import { watch } from 'vue';

type Producto = { id_producto: number; nombre: string; sku: string };
type Proveedor = { id_proveedor: number; nombre_proveedor: string };

defineProps<{
    productos: Producto[];
    proveedores: Proveedor[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Compras', href: '/compras' },
            { title: 'Registrar compra', href: '/compras/crear' },
        ],
    },
});

const hoy = new Date().toISOString().slice(0, 10);

const form = useForm({
    id_producto: '',
    id_proveedor: '',
    cantidad: 1,
    precio_compra: '',
    fecha_compra: hoy,
    fecha_vencimiento: '',
});

let precioEditadoManualmente = false;

watch(
    () => form.id_producto,
    async (idProducto) => {
        if (precioEditadoManualmente || !idProducto) return;

        const response = await fetch(`/compras/ultimo-precio?id_producto=${idProducto}`);
        const data = await response.json();

        if (data.precio_compra) {
            form.precio_compra = data.precio_compra;
        }
    },
);

function onPrecioInput() {
    precioEditadoManualmente = true;
}

const totalCompra = computed(() => {
    const cantidad = Number(form.cantidad) || 0;
    const precio = Number(form.precio_compra) || 0;
    return (cantidad * precio).toFixed(2);
});

function submit() {
    form.post('/compras');
}
</script>

<template>
    <Head title="Registrar compra" />

    <div class="flex flex-col gap-6 p-4 max-w-2xl">
        <h1 class="text-xl font-semibold">Registrar compra</h1>

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-2">
                <Label for="id_producto">Producto</Label>
                <select
                    id="id_producto"
                    v-model="form.id_producto"
                    required
                    class="rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                >
                    <option value="" disabled>Seleccioná un producto</option>
                    <option
                        v-for="producto in productos"
                        :key="producto.id_producto"
                        :value="producto.id_producto"
                    >
                        {{ producto.nombre }} ({{ producto.sku.trim() }})
                    </option>
                </select>
                <InputError :message="form.errors.id_producto" />
            </div>

            <div class="grid gap-2">
                <Label for="id_proveedor">Proveedor</Label>
                <select
                    id="id_proveedor"
                    v-model="form.id_proveedor"
                    required
                    class="rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                >
                    <option value="" disabled>Seleccioná un proveedor</option>
                    <option
                        v-for="proveedor in proveedores"
                        :key="proveedor.id_proveedor"
                        :value="proveedor.id_proveedor"
                    >
                        {{ proveedor.nombre_proveedor }}
                    </option>
                </select>
                <InputError :message="form.errors.id_proveedor" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="cantidad">Cantidad</Label>
                    <Input id="cantidad" type="number" min="1" v-model="form.cantidad" required />
                    <InputError :message="form.errors.cantidad" />
                </div>

                <div class="grid gap-2">
                    <Label for="precio_compra">Precio unitario</Label>
                    <Input
                        id="precio_compra"
                        type="number"
                        step="0.01"
                        min="0"
                        v-model="form.precio_compra"
                        required
                        @input="onPrecioInput"
                    />
                    <InputError :message="form.errors.precio_compra" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="fecha_compra">Fecha de compra</Label>
                    <Input id="fecha_compra" type="date" v-model="form.fecha_compra" required />
                    <InputError :message="form.errors.fecha_compra" />
                </div>

                <div class="grid gap-2">
                    <Label for="fecha_vencimiento">Fecha de vencimiento (opcional)</Label>
                    <Input id="fecha_vencimiento" type="date" v-model="form.fecha_vencimiento" />
                    <InputError :message="form.errors.fecha_vencimiento" />
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button type="submit" :disabled="form.processing">
                    <Spinner v-if="form.processing" />
                    Registrar compra
                </Button>
                <Link href="/compras" class="text-sm text-muted-foreground hover:underline">
                    Cancelar
                </Link>
                <div class="rounded-lg bg-muted/50 p-4 flex items-center justify-between">
                    <span class="text-sm text-muted-foreground">Total de la compra</span>
                    <span class="text-lg font-semibold">Q{{ totalCompra }}</span>
                </div>
            </div>
        </form>
    </div>
</template>
