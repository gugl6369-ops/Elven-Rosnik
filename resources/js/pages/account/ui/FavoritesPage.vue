<script setup lang="ts">
import type { FavoritesPageProps } from '@/entities/plant';
import { Catalog } from '@/widgets/catalog';
import { AppShell } from '@/widgets/layout';
import { RecipeCard } from '@/entities/recipe';
import { AppLink } from '@/shared/ui';

const props = defineProps<Pick<FavoritesPageProps, 'plants' | 'recipes'>>();
</script>

<template>
    <AppShell>
        <div class="favorites-page">
            <Catalog
                :plants="props.plants"
                title="Избранное"
                subtitle="Сохранённые растения и рецепты."
                empty-title="В избранном пока нет растений."
                empty-hint="Добавляйте растения в избранное с карточек в каталоге."
            />

            <section class="favorites-recipes" aria-labelledby="favorites-recipes-title">
                <h2 id="favorites-recipes-title" class="favorites-recipes__title">Рецепты</h2>
                <div v-if="recipes.data.length" class="favorites-recipes__grid">
                    <RecipeCard v-for="recipe in recipes.data" :key="recipe.id" :recipe="recipe" />
                </div>
                <p v-else class="favorites-recipes__empty">
                    Пока нет сохранённых рецептов. Откройте рецепт и нажмите «В избранное».
                </p>
                <nav v-if="recipes.last_page > 1" class="favorites-recipes__pagination" aria-label="Страницы рецептов">
                    <AppLink
                        v-for="link in recipes.links"
                        :key="`${link.label}-${link.url ?? 'x'}`"
                        :href="link.url ?? '#'"
                        class="favorites-recipes__page-link"
                        :class="{
                            'favorites-recipes__page-link--active': link.active,
                            'favorites-recipes__page-link--disabled': !link.url,
                        }"
                        :aria-disabled="!link.url ? 'true' : undefined"
                    >
                        <span v-html="link.label" />
                    </AppLink>
                </nav>
            </section>
        </div>
    </AppShell>
</template>

<style scoped>
.favorites-page {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.favorites-recipes {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.favorites-recipes__title {
    margin: 0;
    color: #203f39;
    font-size: 1.5rem;
}

.favorites-recipes__grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(16rem, 1fr));
    gap: 1rem;
}

.favorites-recipes__empty {
    margin: 0;
    padding: 1.5rem;
    border: 1px dashed #b8cdc6;
    border-radius: 1rem;
    color: #5a746c;
    text-align: center;
}

.favorites-recipes__pagination {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.35rem;
}

.favorites-recipes__page-link {
    display: inline-flex;
    min-width: 2.25rem;
    justify-content: center;
    padding: 0.35rem 0.6rem;
    border: 1px solid #d7e5e0;
    border-radius: 0.5rem;
    background: #fff;
    color: #21483f;
    font-size: 0.875rem;
    text-decoration: none;
}

.favorites-recipes__page-link--active {
    border-color: #21483f;
    background: #21483f;
    color: #fff;
}

.favorites-recipes__page-link--disabled {
    opacity: 0.45;
    pointer-events: none;
}
</style>
