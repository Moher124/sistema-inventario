<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Productos', href: '/productos' },
            { title: 'Nuevo producto', href: '/productos/crear' },
        ],
    },
});

const form = useForm({
    sku: '',
    nombre: '',
    precio_venta: '',
    disponible: true as boolean,
    tipo_producto: '',
    presentacion: '',
    unidades_por_empaque: 1,
    descripcion: '',
    ingredientes: '',
});

let skuEditadoManualmente = false;
let debounceTimer: ReturnType<typeof setTimeout> | null = null;

watch(
    () => form.tipo_producto,
    (tipoProducto) => {
        if (skuEditadoManualmente || !tipoProducto) return;

        if (debounceTimer) clearTimeout(debounceTimer);

        debounceTimer = setTimeout(async () => {
            const response = await fetch(
                `/productos/sugerir-sku?tipo_producto=${encodeURIComponent(tipoProducto)}`,
            );
            const data = await response.json();
            form.sku = data.sku;
        }, 400);
    },
);

function onSkuInput() {
    skuEditadoManualmente = true;
}

function submit() {
    form.post('/productos');
}
</script>

<template>
    <Head title="Nuevo producto" />

    <div class="flex flex-col gap-6 p-4 max-w-2xl">
        <h1 class="text-xl font-semibold">Nuevo producto</h1>

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="sku">SKU</Label>
                    <Input
                        id="sku"
                        v-model="form.sku"
                        required
                        maxlength="13"
                        @input="onSkuInput"
                    />
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
                class="border-2 border-slate-400 dark:border-slate-500"
                />
                <span>Disponible</span>
            </Label>

            <div class="flex items-center gap-4">
                <Button type="submit" :disabled="form.processing">
                    <Spinner v-if="form.processing" />
                    Guardar
                </Button>
                <Link href="/productos" class="text-sm text-muted-foreground">
                    Cancelar
                </Link>
            </div>
        </form>
    </div>
</template>
