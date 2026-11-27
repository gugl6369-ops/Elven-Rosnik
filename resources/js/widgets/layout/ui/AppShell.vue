<script setup lang="ts">
import AppHeader from './AppHeader.vue';

withDefaults(
    defineProps<{
        maxWidthClass?: string;
    }>(),
    {
        maxWidthClass: 'max-w-6xl',
    },
);
</script>

<template>
    <div class="min-h-screen bg-[#f5f8f7] text-[#203f39]">
        <AppHeader />
        <div
            class="mx-auto w-full px-5 py-8 md:px-6 md:py-10"
            :class="maxWidthClass"
        >
            <slot />
        </div>
    </div>
</template>