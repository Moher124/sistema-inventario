<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { Package } from '@lucide/vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <div class="flex size-9 items-center justify-center rounded-xl bg-neutral-900 dark:bg-neutral-800 shadow-sm border border-neutral-800 dark:border-neutral-700">
        <svg xmlns="http://w3.org" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5 text-emerald-400">
            <path d="m21.16 8.37-9-5a1 1 0 0 0-.95 0l-9 5A1 1 0 0 0 1.74 9.24l9 5a1 1 0 0 0 .95 0l9-5a1 1 0 0 0 .47-.87Z"/>
            <path d="M12 22V13.5"/>
            <path d="M1.74 9.24v6.52a1 1 0 0 0 .48.87l9 5a1 1 0 0 0 .95 0l9-5a1 1 0 0 0 .48-.87V9.24"/>
            <path d="M12 13.5 1.74 7.82"/>
            <path d="m12 13.5 10.26-5.68"/>
        </svg>
    </div>
</template>
