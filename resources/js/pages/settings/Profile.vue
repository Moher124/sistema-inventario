<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Profile settings',
                href: '/settings/profile',
            },
        ],
    },
});

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <Head title="Profile settings" />

    <h1 class="sr-only">Profile settings</h1>

    <div class="flex flex-col space-y-6">
        <Heading
            variant="small"
            title="Profile"
            description="Actualizá tu nombre completo"
        />

        <Form
            method="patch"
            action="/settings/profile"
            class="space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="nombre_completo">Nombre completo</Label>
                <Input
                    id="nombre_completo"
                    class="mt-1 block w-full"
                    name="nombre_completo"
                    :default-value="user.nombre_completo"
                    required
                />
                <InputError class="mt-2" :message="errors.nombre_completo" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing" data-test="update-profile-button"
                    >Save</Button
                >
            </div>
        </Form>
    </div>
</template>
