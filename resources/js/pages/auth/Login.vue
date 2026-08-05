<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

defineOptions({
    layout: {
        title: 'Iniciar sesión',
        description: 'Ingresá tu usuario y contraseña',
    },
});

const form = useForm({
    nombre_usuario: '',
    password: '',
});

function submit() {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="Iniciar sesión" />

    <form @submit.prevent="submit" class="flex flex-col gap-6">
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="nombre_usuario">Usuario</Label>
                <Input
                    id="nombre_usuario"
                    type="text"
                    v-model="form.nombre_usuario"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="username"
                />
                <InputError :message="form.errors.nombre_usuario" />
            </div>

            <div class="grid gap-2">
                <Label for="password">Contraseña</Label>
                <PasswordInput
                    id="password"
                    v-model="form.password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                />
                <InputError :message="form.errors.password" />
            </div>

            <Button
                type="submit"
                class="mt-4 w-full"
                :tabindex="3"
                :disabled="form.processing"
            >
                <Spinner v-if="form.processing" />
                Iniciar sesión
            </Button>
        </div>
    </form>
</template>
