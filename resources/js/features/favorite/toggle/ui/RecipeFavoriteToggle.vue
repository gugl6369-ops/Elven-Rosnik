<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, toRef } from 'vue';

import { useRecipeFavoriteToggle } from '../useRecipeFavoriteToggle';

const props = withDefaults(defineProps<{
    recipeId: number;
    isFavorited?: boolean;
    iconOnly?: boolean;
}>(), { isFavorited: false, iconOnly: false });

const page = usePage<{ auth: { user: unknown | null } }>();
const isAuthenticated = computed(() => page.props.auth.user !== null);
const isFavoritedRef = toRef(props, 'isFavorited');
const { favorited, busy, toggle } = useRecipeFavoriteToggle(
    props.recipeId,
    isFavoritedRef,
);
</script>

<template>
    <button
        v-if="isAuthenticated"
        type="button"
        class="recipe-favorite"
        :class="{
            'recipe-favorite--active': favorited,
            'recipe-favorite--icon-only': iconOnly,
        }"
        :aria-pressed="favorited"
        :aria-label="favorited ? 'Убрать из избранного' : 'Добавить в избранное'"
        :title="favorited ? 'Убрать из избранного' : 'Добавить в избранное'"
        :disabled="busy"
        @click.stop="toggle"
    >
        <span aria-hidden="true">{{ favorited ? '★' : '☆' }}</span>
        <span v-if="!iconOnly" class="recipe-favorite__text">
            {{ favorited ? 'В избранном' : 'В избранное' }}
        </span>
    </button>
</template>

<style scoped>
.recipe-favorite {
    display: inline-flex;
    flex: 0 0 auto;
    align-items: center;
    gap: 0.4rem;
    padding: 0.45rem 0.7rem;
    border: 1px solid #d7e5e0;
    border-radius: 0.65rem;
    background: #fff;
    color: #46645a;
    font: inherit;
    font-size: 0.78rem;
    cursor: pointer;
}

.recipe-favorite--active {
    border-color: #d7bd67;
    background: #fff8e3;
    color: #725b12;
}

.recipe-favorite--icon-only {
    width: 2.35rem;
    height: 2.35rem;
    justify-content: center;
    padding: 0;
    border-radius: 50%;
}

.recipe-favorite--icon-only > span:first-child {
    font-size: 1.25rem;
    line-height: 1;
}

.recipe-favorite:disabled {
    cursor: wait;
    opacity: 0.7;
}

.recipe-favorite:focus-visible {
    outline: 2px solid #21483f;
    outline-offset: 2px;
}

</style>
