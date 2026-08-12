<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Proveedores', href: '/proveedores' },
            { title: 'Nuevo proveedor', href: '/proveedores/crear' },
        ],
    },
});

const form = useForm({
    nombre_proveedor: '',
    nit_proveedor: '',
    nombre_representante: '',
    telefono: '',
    email: '',
    direccion: '',
    activo: true as boolean,
});

function submit() {
    form.post('/proveedores');
}
</script>

<template>
    <Head title="Nuevo proveedor" />

    <div class="flex flex-col gap-6 p-4 max-w-2xl">
        <h1 class="text-xl font-semibold">Nuevo proveedor</h1>

        <form @submit.prevent="submit" class="flex flex-col gap-8">
            <section class="flex flex-col gap-4">
                <h2 class="text-sm font-medium text-muted-foreground uppercase tracking-wide">
                    Datos generales
                </h2>

                <div class="grid gap-2">
                    <Label for="nombre_proveedor">Nombre del proveedor</Label>
                    <Input id="nombre_proveedor" v-model="form.nombre_proveedor" required />
                    <InputError :message="form.errors.nombre_proveedor" />
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="nit_proveedor">NIT</Label>
                        <Input id="nit_proveedor" v-model="form.nit_proveedor" required />
                        <InputError :message="form.errors.nit_proveedor" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="nombre_representante">Representante</Label>
                        <Input id="nombre_representante" v-model="form.nombre_representante" required />
                        <InputError :message="form.errors.nombre_representante" />
                    </div>
                </div>
            </section>

            <section class="flex flex-col gap-4 border-t border-sidebar-border/70 pt-6 dark:border-sidebar-border">
                <h2 class="text-sm font-medium text-muted-foreground uppercase tracking-wide">
                    Contacto (opcional)
                </h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="telefono">Teléfono</Label>
                        <Input id="telefono" v-model="form.telefono" />
                        <InputError :message="form.errors.telefono" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Correo</Label>
                        <Input id="email" v-model="form.email" type="email" />
                        <InputError :message="form.errors.email" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="direccion">Dirección</Label>
                    <textarea
                        id="direccion"
                        v-model="form.direccion"
                        class="min-h-20 rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                    />
                    <InputError :message="form.errors.direccion" />
                </div>
            </section>

            <div class="flex items-center justify-between border-t border-sidebar-border/70 pt-6 dark:border-sidebar-border">
                <Label class="flex items-center gap-2">
                    <Checkbox
                        v-model="form.activo"
                        class="border-2 border-slate-400 dark:border-slate-500"
                    />
                    <span>Proveedor activo</span>
                </Label>

                <div class="flex items-center gap-4">
                    <Link href="/proveedores" class="text-sm text-muted-foreground hover:underline">
                        Cancelar
                    </Link>
                    <Button type="submit" :disabled="form.processing">
                        <Spinner v-if="form.processing" />
                        Crear proveedor
                    </Button>
                </div>
            </div>
        </form>
    </div>
</template>
