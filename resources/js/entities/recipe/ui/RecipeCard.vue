<script setup lang="ts">
import type { RecipeCardItem } from '../model/types';
import { AppLink } from '@/shared/ui';
import { RecipeFavoriteToggle } from '@/features/favorite';

defineProps<{ recipe: RecipeCardItem }>();
</script>

<template>
    <article class="recipe-card">
        <header class="recipe-card__header">
            <AppLink :href="`/catalog/recipes/${recipe.id}`" class="recipe-card__title">
                {{ recipe.name }}
            </AppLink>
            <RecipeFavoriteToggle
                :recipe-id="recipe.id"
                :is-favorited="recipe.is_favorited ?? false"
                icon-only
            />
        </header>
        <span v-if="recipe.healthy" class="recipe-card__badge">Полезное</span>
        <p v-if="recipe.description" class="recipe-card__description">
            {{ recipe.description }}
        </p>
        <ul v-if="recipe.plants.length" class="recipe-card__plants" aria-label="Растения в рецепте">
            <li v-for="plant in recipe.plants" :key="plant.id">{{ plant.name }}</li>
        </ul>
        <AppLink :href="`/catalog/recipes/${recipe.id}`" class="recipe-card__link">
            Открыть рецепт <span aria-hidden="true">→</span>
        </AppLink>
    </article>
</template>

<style scoped>
.recipe-card {
    display: flex;
    min-height: 12rem;
    flex-direction: column;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1.25rem;
    border: 1px solid #d7e5e0;
    border-radius: 1rem;
    background: #fff;
    color: inherit;
    transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;
}

.recipe-card:hover {
    transform: translateY(-2px);
    border-color: #9ebbb1;
    box-shadow: 0 8px 24px rgb(32 63 57 / 10%);
}

.recipe-card__header {
    display: flex;
    width: 100%;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.5rem;
}

.recipe-card__title:focus-visible,
.recipe-card__link:focus-visible {
    outline: 2px solid #21483f;
    outline-offset: 3px;
}

.recipe-card__badge {
    padding: 0.2rem 0.55rem;
    border-radius: 999px;
    background: #e4f3ee;
    color: #2a6b5c;
    font-size: 0.7rem;
    font-weight: 600;
}

.recipe-card__title {
    margin: 0;
    color: #203f39;
    font-size: 1.05rem;
    line-height: 1.3;
    font-weight: 600;
    text-decoration: none;
}

.recipe-card__title:hover,
.recipe-card__link:hover {
    text-decoration: underline;
}

.recipe-card__description {
    display: -webkit-box;
    margin: 0;
    overflow: hidden;
    color: #5a746c;
    font-size: 0.85rem;
    line-height: 1.45;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
}

.recipe-card__plants {
    display: flex;
    flex-wrap: wrap;
    gap: 0.35rem;
    margin: 0;
    padding: 0;
    list-style: none;
}

.recipe-card__plants li {
    padding: 0.25rem 0.5rem;
    border-radius: 999px;
    background: #f0f5f2;
    color: #46645a;
    font-size: 0.72rem;
}

.recipe-card__link {
    margin-top: auto;
    color: #2f6d61;
    font-size: 0.82rem;
    font-weight: 600;
    text-decoration: none;
}
</style>
