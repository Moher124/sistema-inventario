<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

type Producto = {
    id_producto: number;
    sku: string;
    nombre: string;
    precio_venta: string;
    disponible: boolean;
    tipo_producto: string | null;
    presentacion: string | null;
    unidades_por_empaque: number | null;
    descripcion: string | null;
    ingredientes: string | null;
};

const props = defineProps<{
    producto: Producto;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Productos', href: '/productos' },
            { title: 'Editar producto', href: '/productos' },
        ],
    },
});

const form = useForm({
    sku: props.producto.sku,
    nombre: props.producto.nombre,
    precio_venta: props.producto.precio_venta,
    disponible: props.producto.disponible,
    tipo_producto: props.producto.tipo_producto ?? '',
    presentacion: props.producto.presentacion ?? '',
    unidades_por_empaque: props.producto.unidades_por_empaque ?? 1,
    descripcion: props.producto.descripcion ?? '',
    ingredientes: props.producto.ingredientes ?? '',
});

function submit() {
    form.put(`/productos/${props.producto.id_producto}`);
}
</script>

<template>
    <Head title="Editar producto" />

    <div class="flex flex-col gap-6 p-4 max-w-2xl">
        <h1 class="text-xl font-semibold">Editar producto</h1>

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="sku">SKU</Label>
                    <Input id="sku" v-model="form.sku" required maxlength="13" />
                    <InputError :message="form.errors.sku" />
                </div>

                <div class="grid gap-2">
                    <Label for="precio_venta">Precio de venta</Label>
                    <Input
                        id="precio_venta"
                        type="number"
                        step="0.01"
                        min="0"
                        v-model="form.precio_venta"
                        required
                    />
                    <InputError :message="form.errors.precio_venta" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="nombre">Nombre</Label>
                <Input id="nombre" v-model="form.nombre" required maxlength="150" />
                <InputError :message="form.errors.nombre" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="tipo_producto">Tipo de producto</Label>
                    <Input id="tipo_producto" v-model="form.tipo_producto" required />
                    <InputError :message="form.errors.tipo_producto" />
                </div>

                <div class="grid gap-2">
                    <Label for="presentacion">Presentación</Label>
                    <Input id="presentacion" v-model="form.presentacion" required />
                    <InputError :message="form.errors.presentacion" />
                </div>
            </div>

            <div class="grid gap-2 max-w-xs">
                <Label for="unidades_por_empaque">Unidades por empaque</Label>
                <Input
                    id="unidades_por_empaque"
                    type="number"
                    min="1"
                    v-model="form.unidades_por_empaque"
                    required
                />
                <InputError :message="form.errors.unidades_por_empaque" />
            </div>

            <div class="grid gap-2">
                <Label for="descripcion">Descripción (opcional)</Label>
                <textarea
                    id="descripcion"
                    v-model="form.descripcion"
                    class="min-h-20 rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                />
                <InputError :message="form.errors.descripcion" />
            </div>

            <div class="grid gap-2">
                <Label for="ingredientes">Ingredientes (opcional)</Label>
                <textarea
                    id="ingredientes"
                    v-model="form.ingredientes"
                    class="min-h-20 rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                />
                <InputError :message="form.errors.ingredientes" />
            </div>

            <Label class="flex items-center gap-2">
                <Checkbox v-model="form.disponible"
                class="border-2 border-slate-400 dark:border-slate-500"/>
                <span>Disponible</span>
            </Label>

            <div class="flex items-center gap-4">
                <Button type="submit" :disabled="form.processing">
                    <Spinner v-if="form.processing" />
                    Guardar cambios
                </Button>
                <Link href="/productos" class="text-sm text-muted-foreground">
                    Cancelar
                </Link>
            </div>
        </form>
    </div>
</template>
