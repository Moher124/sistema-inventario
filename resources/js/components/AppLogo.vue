<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

const name = usePage().props.name;
</script>

<template>
    <!-- Contenedor del Icono: Ahora con diseño premium y bordes redondeados estilizados -->
    <div
        class="flex aspect-square size-9 items-center justify-center rounded-xl bg-neutral-900 border border-neutral-800 dark:bg-neutral-950 dark:border-neutral-800/60 shadow-sm transition-all duration-200"
    >
        <!-- Tu nuevo Cubo Isométrico 3D integrado de forma limpia -->
        <svg xmlns="http://w3.org" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5 text-emerald-400 animate-pulse-slow">
            <path d="m21.16 8.37-9-5a1 1 0 0 0-.95 0l-9 5A1 1 0 0 0 1.74 9.24l9 5a1 1 0 0 0 .95 0l9-5a1 1 0 0 0 .47-.87Z"/>
            <path d="M12 22V13.5"/>
            <path d="M1.74 9.24v6.52a1 1 0 0 0 .48.87l9 5a1 1 0 0 0 .95 0l9-5a1 1 0 0 0 .48-.87V9.24"/>
            <path d="M12 13.5 1.74 7.82"/>
            <path d="m12 13.5 10.26-5.68"/>
        </svg>
    </div>

    <!-- Contenedor del Texto: Tipografía refinada con tracking cerrado -->
    <div class="ml-3 grid flex-1 text-left text-sm">
        <span class="truncate leading-none font-bold tracking-tight text-neutral-800 dark:text-neutral-100 text-[14px]">
            {{ name || 'Inventario' }}
        </span>
        <span class="mt-0.5 truncate text-[11px] leading-none font-medium text-emerald-500/90 dark:text-emerald-400/80 tracking-wide uppercase">
            Sistema Control
        </span>
    </div>
</template>

<style scoped>
/* Un pequeño efecto visual sutil para que el logo se sienta vivo */
.animate-pulse-slow {
    animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.85; }
}
</style>
