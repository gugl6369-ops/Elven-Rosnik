<script setup lang="ts">
/**
 * Alias page for the current Inertia component name `catalog/ui/PlantPage`
 * (backend target until renamed to `PlantShowPage`).
 */
import type { PlantShowPageProps } from '@/entities/plant';
import PlantShowPage from './PlantShowPage.vue';

defineProps<Pick<PlantShowPageProps, 'plant'>>();
</script>

<template>
    <PlantShowPage :plant="plant" />
</template>