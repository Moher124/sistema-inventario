<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

type Usuario = {
    id_usuario: number;
    nombre_usuario: string;
    nombre_completo: string;
    rol: 'admin' | 'empleado';
    activo: boolean;
};

defineProps<{
    usuarios: Usuario[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Usuarios', href: '/usuarios' }],
    },
});

function desactivar(usuario: Usuario) {
    if (!confirm(`¿Desactivar al usuario "${usuario.nombre_completo}"?`)) {
        return;
    }

    router.delete(`/usuarios/${usuario.id_usuario}`);
}
</script>

<template>
    <Head title="Usuarios" />

    <div class="flex flex-col gap-4 p-4">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold">Usuarios</h1>
            <Button as-child>
                <Link href="/usuarios/crear">Nuevo usuario</Link>
            </Button>
        </div>

        <div class="overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <table class="w-full text-sm">
                <thead class="bg-muted/50 text-left">
                    <tr>
                        <th class="p-3">Usuario</th>
                        <th class="p-3">Nombre completo</th>
                        <th class="p-3">Rol</th>
                        <th class="p-3">Estado</th>
                        <th class="p-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="usuario in usuarios"
                        :key="usuario.id_usuario"
                        class="border-t border-sidebar-border/70 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-mono text-xs">{{ usuario.nombre_usuario }}</td>
                        <td class="p-3">{{ usuario.nombre_completo }}</td>
                        <td class="p-3">
                            <span
                                class="rounded-full px-2 py-0.5 text-xs font-medium"
                                :class="usuario.rol === 'admin'
                                    ? 'bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-400'
                                    : 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-400'"
                            >
                                {{ usuario.rol === 'admin' ? 'Administrador' : 'Empleado' }}
                            </span>
                        </td>
                        <td class="p-3">
                            <span :class="usuario.activo ? 'text-green-600' : 'text-red-600'">
                                {{ usuario.activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="p-3 text-right">
                            <div class="flex justify-end gap-2">
                                <Link
                                    :href="`/usuarios/${usuario.id_usuario}/editar`"
                                    class="text-sm text-blue-600 hover:underline"
                                >
                                    Editar
                                </Link>
                                <button
                                    v-if="usuario.activo"
                                    type="button"
                                    class="text-sm text-red-600 hover:underline"
                                    @click="desactivar(usuario)"
                                >
                                    Desactivar
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="usuarios.length === 0">
                        <td colspan="5" class="p-3 text-center text-muted-foreground">
                            No hay usuarios registrados.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
