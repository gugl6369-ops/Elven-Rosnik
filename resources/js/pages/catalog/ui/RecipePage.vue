<script setup lang="ts">
/**
 * Inertia page name `catalog/ui/RecipePage` (alias for RecipeShowPage).
 */
import type { RecipeShowPageProps } from '@/entities/recipe';
import RecipeShowPage from './RecipeShowPage.vue';

defineProps<Pick<RecipeShowPageProps, 'recipe'>>();
</script>

<template>
    <RecipeShowPage :recipe="recipe" />
</template>