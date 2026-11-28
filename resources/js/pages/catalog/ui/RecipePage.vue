<script setup lang="ts">
import type { RecipeShowPageProps } from '@/entities/recipe';
import RecipeShowPage from './RecipeShowPage.vue';

defineProps<Pick<RecipeShowPageProps, 'recipe'>>();
</script>

<template>
    <RecipeShowPage :recipe="recipe" />
</template>
