<script setup lang="ts">
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        isFavorited?: boolean;
        favoritesCount?: number;
        /** When false, control is display-only (no POST until API exists). */
        interactive?: boolean;
    }>(),
    {
        isFavorited: false,
        favoritesCount: 0,
        interactive: false,
    },
);

const emit = defineEmits<{
    toggle: [];
}>();

const label = computed(() =>
    props.isFavorited ? 'В избранном' : 'Добавить в избранное',
);

function onToggle(): void {
    if (!props.interactive) {
        return;
    }

    emit('toggle');
}
</script>

<template>
    <div class="plant-favorite">
        <button
            type="button"
            class="plant-favorite__control"
            :class="{ 'plant-favorite__control--active': isFavorited }"
            :aria-pressed="isFavorited"
            :aria-label="label"
            :disabled="!interactive"
            :title="
                interactive
                    ? label
                    : 'Избранное: только просмотр до появления API'
            "
            @click="onToggle"
        >
            <span class="plant-favorite__icon" aria-hidden="true">
                {{ isFavorited ? '★' : '☆' }}
            </span>
            <span class="plant-favorite__text">{{ label }}</span>
        </button>
        <p
            v-if="favoritesCount > 0"
            class="plant-favorite__count"
        >
            {{ favoritesCount }}
            {{ favoritesCount === 1 ? 'пользователь' : 'пользователей' }}
            добавили в избранное
        </p>
    </div>
</template>

<style scoped>
.plant-favorite {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}

.plant-favorite__control {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.85rem;
    border-radius: 0.625rem;
    border: 1px solid #d7e5e0;
    background: #fff;
    font-size: 0.875rem;
    color: #203f39;
    cursor: pointer;
}

.plant-favorite__control:disabled {
    cursor: default;
    opacity: 0.92;
}

.plant-favorite__control--active {
    border-color: #c9a227;
    background: #fff9e8;
}

.plant-favorite__icon {
    font-size: 1.125rem;
    line-height: 1;
}

.plant-favorite__count {
    margin: 0;
    font-size: 0.8125rem;
    color: #78928a;
}
</style>