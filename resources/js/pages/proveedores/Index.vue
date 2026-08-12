<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

type Proveedor = {
    id_proveedor: number;
    nombre_proveedor: string;
    telefono: string;
    email: string;
    direccion: string;
    nit_proveedor: string;
    nombre_representante: string;
    activo: boolean;

};

defineProps<{
    proveedores: Proveedor[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Proveedores', href: '/proveedores' },
        ],
    },
});

function eliminar(proveedor: Proveedor) {
    if (!confirm(`¿Eliminar el proveedor "${proveedor.nombre_proveedor}"? Esta acción no se puede deshacer.`)) {
        return;
    }

    router.delete(`/proveedores/${proveedor.id_proveedor}`);
}
</script>

<template>
    <Head title="Proveedores" />

    <div class="flex flex-col gap-4 p-4">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold">Proveedores</h1>
            <Button as-child>
                <Link href="/proveedores/crear">Nuevo proveedor</Link>
            </Button>
        </div>

        <div class="overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <table class="w-full text-sm">
                <thead class="bg-muted/50 text-left">
                    <tr>
                        <th class="p-3">Nombre</th>
                        <th class="p-3">Teléfono</th>
                        <th class="p-3">Correo</th>
                        <th class="p-3">Dirección</th>
                        <th class="p-3">NIT</th>
                        <th class="p-3">Representante</th>
                        <th class="p-3">Activo</th>
                        <th class="p-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="proveedor in proveedores" :key="proveedor.id_proveedor" class="border-b border-sidebar-border/70 dark:border-sidebar-border">
                        <td class="p-3">{{ proveedor.nombre_proveedor }}</td>
                        <td class="p-3">{{ proveedor.telefono }}</td>
                        <td class="p-3">{{ proveedor.email }}</td>
                        <td class="p-3">{{ proveedor.direccion }}</td>
                        <td class="p-3">{{ proveedor.nit_proveedor }}</td>
                        <td class="p-3">{{ proveedor.nombre_representante }}</td>
                        <td class="p-3">
                            <span :class="{ 'text-green-500': proveedor.activo, 'text-red-500': !proveedor.activo }">
                                {{ proveedor.activo ? 'Sí' : 'No' }}
                            </span>
                        </td>
                        <td class="p-3">
                            <div class="flex gap-2">
                                <Button as-child class="bg-blue-600 hover:bg-blue-700 text-white">
                                    <Link :href="`/proveedores/${proveedor.id_proveedor}/editar`">Editar</Link>
                                </Button>
                                <Button variant="destructive" @click="eliminar(proveedor)">Eliminar</Button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>


