<script setup lang="ts">
import type { RecipeSection } from '@/entities/recipe';
import { RecipeCard } from '@/entities/recipe';

defineProps<{ section: RecipeSection }>();
</script>

<template>
    <section class="recipe-recommendations" aria-label="Рецепты к растениям поблизости">
        <header class="recipe-recommendations__header">
            <div>
                <p class="recipe-recommendations__eyebrow">Подборка по растениям рядом</p>
                <h2 class="recipe-recommendations__title">Рецепты для вдохновения</h2>
            </div>
            <span class="recipe-recommendations__count">{{ section.recipes.length }} рецептов</span>
        </header>
        <div class="recipe-recommendations__grid">
            <RecipeCard v-for="recipe in section.recipes" :key="recipe.id" :recipe="recipe" />
        </div>
    </section>
</template>

<style scoped>
.recipe-recommendations {
    grid-column: 1 / -1;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin: 0.25rem 0 0.75rem;
    padding: clamp(1rem, 2.5vw, 1.5rem);
    border: 1px solid #dce9e3;
    border-radius: 1.25rem;
    background: linear-gradient(145deg, #f3f8f5 0%, #edf4ef 100%);
}

.recipe-recommendations__header {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
}

.recipe-recommendations__eyebrow {
    margin: 0 0 0.25rem;
    color: #648078;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.recipe-recommendations__title {
    margin: 0;
    color: #203f39;
    font-size: clamp(1.15rem, 2vw, 1.4rem);
}

.recipe-recommendations__count {
    color: #648078;
    font-size: 0.8rem;
}

.recipe-recommendations__grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.85rem;
}

@media (max-width: 900px) {
    .recipe-recommendations__grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}

@media (max-width: 560px) {
    .recipe-recommendations__grid { grid-template-columns: 1fr; }
}
</style>
