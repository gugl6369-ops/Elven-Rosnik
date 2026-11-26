<script setup lang="ts">
import { Link as InertiaLink } from '@inertiajs/vue3';

interface IProps {
    href: string;
}

defineProps<IProps>();
</script>

<template>
    <InertiaLink :href="href">
        <slot />
    </InertiaLink>
</template>