<script setup lang="ts">
import type { PlantDetail } from '@/entities/plant';
import {
    PlantCommentsList,
    PlantFavoriteBadge,
} from '@/entities/plant';
import { computed } from 'vue';

const props = defineProps<{
    plant: PlantDetail;
    isAuthenticated: boolean;
}>();

const genusLabel = computed(() => {
    if (props.plant.genus?.genus_name) {
        return props.plant.genus.genus_name;
    }

    return props.plant.genus_name ?? null;
});

const normalizedHeight = computed(() =>
    props.plant.height !== null && props.plant.height !== undefined
        ? String(props.plant.height)
        : null,
);

const normalizedTempMin = computed(() =>
    props.plant.temp_min !== null && props.plant.temp_min !== undefined
        ? String(props.plant.temp_min)
        : null,
);

const normalizedTempMax = computed(() =>
    props.plant.temp_max !== null && props.plant.temp_max !== undefined
        ? String(props.plant.temp_max)
        : null,
);

function formatTemperature(min: string | null, max: string | null): string | null {
    if (min !== null && max !== null) {
        return `${min}…${max} °C`;
    }

    if (min !== null) {
        return `от ${min} °C`;
    }

    if (max !== null) {
        return `до ${max} °C`;
    }

    return null;
}

const temperatureLabel = computed(() =>
    formatTemperature(normalizedTempMin.value, normalizedTempMax.value),
);

const tags = computed(() => props.plant.tags ?? []);
const recipes = computed(() => props.plant.recipes ?? []);
const comments = computed(() => props.plant.comments ?? []);
const commentsCount = computed(() => props.plant.comments_count ?? comments.value.length);
const favoritesCount = computed(() => props.plant.favorites_count ?? 0);
const isFavorited = computed(() => props.plant.is_favorited ?? false);

const extendedSections = computed(() => {
    const rows: { title: string; text: string }[] = [];

    const fields: { key: keyof PlantDetail; title: string }[] = [
        { key: 'preparation', title: 'Приготовление' },
        { key: 'storage', title: 'Хранение' },
        { key: 'collection', title: 'Сбор' },
        { key: 'readiness_check', title: 'Признаки готовности' },
        { key: 'drying', title: 'Сушка' },
    ];

    for (const { key, title } of fields) {
        const value = props.plant[key];

        if (typeof value === 'string' && value.trim() !== '') {
            rows.push({ title, text: value });
        }
    }

    return rows;
});
</script>

<template>
    <article class="plant-detail">
        <header class="plant-detail__hero">
            <div class="plant-detail__media">
                <img
                    v-if="plant.image_url"
                    :src="plant.image_url"
                    :alt="plant.name"
                    class="plant-detail__image"
                />
                <div v-else class="plant-detail__placeholder" aria-hidden="true">
                    🌿
                </div>
            </div>
            <div class="plant-detail__intro">
                <h1 class="plant-detail__title">{{ plant.name }}</h1>
                <p v-if="genusLabel" class="plant-detail__genus">
                    {{ genusLabel }}
                </p>
                <p
                    v-if="plant.description"
                    class="plant-detail__description"
                >
                    {{ plant.description }}
                </p>
                <PlantFavoriteBadge
                    :is-favorited="isFavorited"
                    :favorites-count="favoritesCount"
                    :interactive="false"
                />
                <p
                    v-if="isAuthenticated && isFavorited"
                    class="plant-detail__fav-hint"
                >
                    Вы отметили это растение в избранном.
                </p>
                <p
                    v-else-if="!isAuthenticated"
                    class="plant-detail__fav-hint"
                >
                    Войдите в аккаунт, чтобы видеть своё избранное.
                </p>
            </div>
        </header>

        <ul class="plant-detail__meta">
            <li v-if="normalizedHeight">
                <span>Высота</span>
                <span>{{ normalizedHeight }} м</span>
            </li>
            <li v-if="plant.watering_frequency">
                <span>Полив</span>
                <span>{{ plant.watering_frequency }}</span>
            </li>
            <li v-if="plant.sunlight">
                <span>Свет</span>
                <span>{{ plant.sunlight }}</span>
            </li>
            <li v-if="temperatureLabel">
                <span>Температура</span>
                <span>{{ temperatureLabel }}</span>
            </li>
        </ul>

        <section
            v-if="tags.length > 0"
            class="plant-detail__section"
            aria-labelledby="plant-tags-heading"
        >
            <h2 id="plant-tags-heading" class="plant-detail__section-title">
                Теги
            </h2>
            <ul class="plant-detail__tags">
                <li
                    v-for="tag in tags"
                    :key="tag.id"
                    class="plant-detail__tag"
                >
                    <span class="plant-detail__tag-name">{{ tag.name }}</span>
                    <span class="plant-detail__tag-category">{{ tag.category }}</span>
                </li>
            </ul>
        </section>

        <section
            v-if="recipes.length > 0"
            class="plant-detail__section"
            aria-labelledby="plant-recipes-heading"
        >
            <h2 id="plant-recipes-heading" class="plant-detail__section-title">
                Рецепты
            </h2>
            <ul class="plant-detail__recipes">
                <li v-for="recipe in recipes" :key="recipe.id">
                    {{ recipe.name }}
                </li>
            </ul>
        </section>

        <section
            v-for="section in extendedSections"
            :key="section.title"
            class="plant-detail__section"
        >
            <h2 class="plant-detail__section-title">
                {{ section.title }}
            </h2>
            <p class="plant-detail__prose">
                {{ section.text }}
            </p>
        </section>

        <PlantCommentsList
            :comments="comments"
            :comments-count="commentsCount"
        />
    </article>
</template>

<style scoped>
.plant-detail {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.plant-detail__hero {
    display: grid;
    gap: 1.25rem;
    border-radius: 1rem;
    border: 1px solid #d7e5e0;
    background: #fff;
    overflow: hidden;
}

@media (min-width: 48rem) {
    .plant-detail__hero {
        grid-template-columns: minmax(12rem, 20rem) 1fr;
    }
}

.plant-detail__media {
    aspect-ratio: 4 / 3;
    background: #e8f0ed;
    min-height: 12rem;
}

.plant-detail__image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.plant-detail__placeholder {
    display: grid;
    place-items: center;
    width: 100%;
    height: 100%;
    font-size: 3rem;
    color: #78928a;
}

.plant-detail__intro {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    padding: 1.25rem 1.25rem 1.5rem;
}

.plant-detail__title {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 600;
    color: #203f39;
    line-height: 1.2;
}

.plant-detail__genus {
    margin: 0;
    font-size: 0.9375rem;
    font-style: italic;
    color: #4a7268;
}

.plant-detail__description {
    margin: 0;
    font-size: 1rem;
    line-height: 1.55;
    color: #5a746c;
}

.plant-detail__fav-hint {
    margin: 0;
    font-size: 0.8125rem;
    color: #78928a;
}

.plant-detail__meta {
    margin: 0;
    padding: 1rem 1.25rem;
    list-style: none;
    display: grid;
    gap: 0.5rem;
    border-radius: 1rem;
    border: 1px solid #d7e5e0;
    background: #fff;
    font-size: 0.9375rem;
}

.plant-detail__meta li {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    color: #203f39;
}

.plant-detail__meta li span:first-child {
    color: #78928a;
}

.plant-detail__section {
    padding: 0 0.25rem;
}

.plant-detail__section-title {
    margin: 0 0 0.65rem;
    font-size: 1.0625rem;
    font-weight: 600;
    color: #203f39;
}

.plant-detail__tags {
    margin: 0;
    padding: 0;
    list-style: none;
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.plant-detail__tag {
    display: inline-flex;
    flex-direction: column;
    gap: 0.1rem;
    padding: 0.4rem 0.65rem;
    border-radius: 0.5rem;
    border: 1px solid #d7e5e0;
    background: #fff;
    font-size: 0.8125rem;
}

.plant-detail__tag-name {
    font-weight: 600;
    color: #21483f;
}

.plant-detail__tag-category {
    color: #78928a;
}

.plant-detail__recipes {
    margin: 0;
    padding-left: 1.25rem;
    color: #203f39;
    line-height: 1.5;
}

.plant-detail__prose {
    margin: 0;
    font-size: 0.9375rem;
    line-height: 1.55;
    color: #5a746c;
    white-space: pre-wrap;
}
</style>