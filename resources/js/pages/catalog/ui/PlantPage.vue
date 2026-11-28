<script setup lang="ts">
import type { PlantShowPageProps } from '@/entities/plant';
import PlantShowPage from './PlantShowPage.vue';

defineProps<Pick<PlantShowPageProps, 'plant'>>();
</script>

<template>
    <PlantShowPage :plant="plant" />
</template>
