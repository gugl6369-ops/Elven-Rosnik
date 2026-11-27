<script setup lang="ts">
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        isFavorited?: boolean;
        favoritesCount?: number;
        interactive?: boolean;
        compact?: boolean;
    }>(),
    {
        isFavorited: false,
        favoritesCount: 0,
        interactive: false,
        compact: false,
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
    <div class="plant-favorite" :class="{ 'plant-favorite--compact': compact }">
        <button
            type="button"
            class="plant-favorite__control"
            :class="{ 'plant-favorite__control--active': isFavorited }"
            :aria-pressed="isFavorited"
            :aria-label="label"
            :disabled="!interactive"
            :title="label"
            @click="onToggle"
        >
            <span class="plant-favorite__icon" aria-hidden="true">
                {{ isFavorited ? '★' : '☆' }}
            </span>
            <span v-if="!compact" class="plant-favorite__text">{{ label }}</span>
        </button>
        <p
            v-if="!compact && favoritesCount > 0"
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

.plant-favorite--compact .plant-favorite__control {
    padding: 0.4rem 0.55rem;
    border-radius: 999px;
    box-shadow: 0 1px 4px rgb(32 63 57 / 15%);
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