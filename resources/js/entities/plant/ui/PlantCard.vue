<script setup lang="ts">
import type { PlantListItem } from '@/entities/plant';

defineProps<{
    plant: PlantListItem;
}>();

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
</script>

<template>
    <article class="plant-card">
        <div class="plant-card__media">
            <img
                v-if="plant.image_url"
                :src="plant.image_url"
                :alt="plant.name"
                class="plant-card__image"
                loading="lazy"
            />
            <div v-else class="plant-card__placeholder" aria-hidden="true">
                🌿
            </div>
        </div>
        <div class="plant-card__body">
            <h2 class="plant-card__title">{{ plant.name }}</h2>
            <p v-if="plant.genus" class="plant-card__genus">
                {{ plant.genus.genus_name }}
            </p>
            <p v-if="plant.description" class="plant-card__description">
                {{ plant.description }}
            </p>
            <ul class="plant-card__meta">
                <li v-if="plant.height">
                    <span>Высота</span>
                    <span>{{ plant.height }} м</span>
                </li>
                <li v-if="plant.watering_frequency">
                    <span>Полив</span>
                    <span>{{ plant.watering_frequency }}</span>
                </li>
                <li v-if="plant.sunlight">
                    <span>Свет</span>
                    <span>{{ plant.sunlight }}</span>
                </li>
                <li v-if="formatTemperature(plant.temp_min, plant.temp_max)">
                    <span>Температура</span>
                    <span>{{ formatTemperature(plant.temp_min, plant.temp_max) }}</span>
                </li>
            </ul>
        </div>
    </article>
</template>

<style scoped>
.plant-card {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border-radius: 1rem;
    border: 1px solid #d7e5e0;
    background: #fff;
    box-shadow: 0 1px 2px rgb(32 63 57 / 6%);
}

.plant-card__media {
    aspect-ratio: 4 / 3;
    background: #e8f0ed;
}

.plant-card__image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.plant-card__placeholder {
    display: grid;
    place-items: center;
    width: 100%;
    height: 100%;
    font-size: 2.5rem;
    color: #78928a;
}

.plant-card__body {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    padding: 1rem 1.125rem 1.25rem;
}

.plant-card__title {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: #203f39;
    line-height: 1.3;
}

.plant-card__genus {
    margin: 0;
    font-size: 0.8125rem;
    font-weight: 500;
    color: #4a7268;
    text-transform: italic;
}

.plant-card__description {
    margin: 0;
    font-size: 0.875rem;
    line-height: 1.45;
    color: #5a746c;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.plant-card__meta {
    margin: 0.25rem 0 0;
    padding: 0;
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    font-size: 0.8125rem;
}

.plant-card__meta li {
    display: flex;
    justify-content: space-between;
    gap: 0.75rem;
    color: #203f39;
}

.plant-card__meta li span:first-child {
    color: #78928a;
}
</style>