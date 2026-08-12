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
            { title: 'Usuarios', href: '/usuarios' },
            { title: 'Nuevo usuario', href: '/usuarios/crear' },
        ],
    },
});

const form = useForm({
    nombre_usuario: '',
    nombre_completo: '',
    password: '',
    rol: 'empleado' as 'admin' | 'empleado',
    activo: true as boolean,
});

function submit() {
    form.post('/usuarios');
}
</script>

<template>
    <Head title="Nuevo usuario" />

    <div class="flex flex-col gap-6 p-4 max-w-xl">
        <h1 class="text-xl font-semibold">Nuevo usuario</h1>

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-2">
                <Label for="nombre_usuario">Usuario (para iniciar sesión)</Label>
                <Input id="nombre_usuario" v-model="form.nombre_usuario" required maxlength="50" />
                <InputError :message="form.errors.nombre_usuario" />
            </div>

            <div class="grid gap-2">
                <Label for="nombre_completo">Nombre completo</Label>
                <Input id="nombre_completo" v-model="form.nombre_completo" required maxlength="150" />
                <InputError :message="form.errors.nombre_completo" />
            </div>

            <div class="grid gap-2">
                <Label for="password">Contraseña</Label>
                <Input id="password" type="password" v-model="form.password" required minlength="8" />
                <InputError :message="form.errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="rol">Rol</Label>
                <select
                    id="rol"
                    v-model="form.rol"
                    required
                    class="rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                >
                    <option value="empleado">Empleado</option>
                    <option value="admin">Administrador</option>
                </select>
                <InputError :message="form.errors.rol" />
            </div>

            <Label class="flex items-center gap-2">
                <Checkbox
                    v-model="form.activo"
                    class="border-2 border-slate-400 dark:border-slate-500"
                />
                <span>Activo</span>
            </Label>

            <div class="flex items-center gap-4">
                <Button type="submit" :disabled="form.processing">
                    <Spinner v-if="form.processing" />
                    Crear usuario
                </Button>
                <Link href="/usuarios" class="text-sm text-muted-foreground hover:underline">
                    Cancelar
                </Link>
            </div>
        </form>
    </div>
</template>
