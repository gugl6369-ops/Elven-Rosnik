<script setup lang="ts">
import type { RecipeDetail } from '@/entities/recipe';
import { EntityCommentsSection } from '@/features/comment';
import { RecipeFavoriteToggle } from '@/features/favorite';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    recipe: RecipeDetail;
    isAuthenticated: boolean;
    currentUserId?: number | null;
    isAdmin?: boolean;
}>();

const plants = computed(() => props.recipe.plants ?? []);
const tags = computed(() => props.recipe.tags ?? []);
const comments = computed(() => props.recipe.comments ?? []);
const commentsCount = computed(
    () => props.recipe.comments_count ?? comments.value.length,
);
</script>

<template>
    <article class="recipe-detail">
        <header class="recipe-detail__header">
            <h1 class="recipe-detail__title">{{ recipe.name }}</h1>
            <RecipeFavoriteToggle
                :recipe-id="recipe.id"
                :is-favorited="recipe.is_favorited ?? false"
            />
            <p v-if="recipe.healthy" class="recipe-detail__badge">
                Полезный рецепт
            </p>
        </header>

        <p v-if="recipe.description" class="recipe-detail__lead">
            {{ recipe.description }}
        </p>

        <section
            v-if="recipe.recipe"
            class="recipe-detail__section"
            aria-labelledby="recipe-body-heading"
        >
            <h2 id="recipe-body-heading" class="recipe-detail__section-title">
                Приготовление
            </h2>
            <p class="recipe-detail__prose">{{ recipe.recipe }}</p>
        </section>

        <section
            v-if="recipe.additional"
            class="recipe-detail__section"
            aria-labelledby="recipe-additional-heading"
        >
            <h2
                id="recipe-additional-heading"
                class="recipe-detail__section-title"
            >
                Дополнительно
            </h2>
            <p class="recipe-detail__prose">{{ recipe.additional }}</p>
        </section>

        <section
            v-if="recipe.advice"
            class="recipe-detail__section"
            aria-labelledby="recipe-advice-heading"
        >
            <h2 id="recipe-advice-heading" class="recipe-detail__section-title">
                Советы
            </h2>
            <p class="recipe-detail__prose">{{ recipe.advice }}</p>
        </section>

        <section
            v-if="plants.length > 0"
            class="recipe-detail__section"
            aria-labelledby="recipe-plants-heading"
        >
            <h2 id="recipe-plants-heading" class="recipe-detail__section-title">
                Растения
            </h2>
            <ul class="recipe-detail__plants">
                <li v-for="plant in plants" :key="plant.id">
                    <Link
                        :href="`/catalog/plants/${plant.id}`"
                        class="recipe-detail__plant-link"
                    >
                        {{ plant.name }}
                    </Link>
                </li>
            </ul>
        </section>

        <section
            v-if="tags.length > 0"
            class="recipe-detail__section"
            aria-labelledby="recipe-tags-heading"
        >
            <h2 id="recipe-tags-heading" class="recipe-detail__section-title">
                Теги
            </h2>
            <div class="recipe-detail__tags">
                <span v-for="tag in tags" :key="tag.id" class="recipe-detail__tag">
                    <small>{{ tag.category }}</small>{{ tag.name }}
                </span>
            </div>
        </section>

        <EntityCommentsSection
            entity-kind="recipe"
            :entity-id="recipe.id"
            reload-prop="recipe"
            :comments="comments"
            :comments-count="commentsCount"
            :is-authenticated="isAuthenticated"
            :current-user-id="currentUserId ?? null"
            :is-admin="isAdmin ?? false"
        />
    </article>
</template>

<style scoped>
.recipe-detail {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.recipe-detail__header {
    display: flex;
    flex-wrap: wrap;
    align-items: baseline;
    gap: 0.75rem;
}

.recipe-detail__title {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 700;
    color: #203f39;
}

.recipe-detail__badge {
    margin: 0;
    padding: 0.2rem 0.55rem;
    border-radius: 999px;
    background: #e4f3ee;
    color: #2a6b5c;
    font-size: 0.75rem;
    font-weight: 600;
}

.recipe-detail__lead {
    margin: 0;
    font-size: 1rem;
    line-height: 1.55;
    color: #3d5650;
}

.recipe-detail__section {
    padding: 1rem 1.25rem;
    border-radius: 1rem;
    border: 1px solid #d7e5e0;
    background: #fff;
}

.recipe-detail__section-title {
    margin: 0 0 0.5rem;
    font-size: 1rem;
    font-weight: 600;
    color: #21483f;
}

.recipe-detail__prose {
    margin: 0;
    font-size: 0.9375rem;
    line-height: 1.55;
    color: #203f39;
    white-space: pre-wrap;
}

.recipe-detail__plants {
    margin: 0;
    padding-left: 1.25rem;
}

.recipe-detail__plant-link {
    color: #2f6d61;
    text-decoration: underline;
}

.recipe-detail__tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
}

.recipe-detail__tag {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.3rem 0.65rem;
    border: 1px solid #d7e5e0;
    border-radius: 999px;
    background: #f5f8f7;
    color: #21483f;
    font-size: 0.8125rem;
}

.recipe-detail__tag small {
    color: #78928a;
    font-size: 0.68rem;
}
</style>
