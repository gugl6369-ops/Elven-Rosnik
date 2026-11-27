<script setup lang="ts">
import { PlantFavoriteBadge } from '@/entities/plant';
import { usePage } from '@inertiajs/vue3';
import { computed, toRef } from 'vue';

import { usePlantFavoriteToggle } from '../usePlantFavoriteToggle';

const props = withDefaults(
    defineProps<{
        plantId: number;
        isFavorited?: boolean;
        favoritesCount?: number;
        compact?: boolean;
    }>(),
    {
        isFavorited: false,
        favoritesCount: 0,
        compact: false,
    },
);

const page = usePage<{ auth: { user: unknown | null } }>();
const isAuthenticated = computed(() => page.props.auth.user !== null);

const isFavoritedRef = toRef(props, 'isFavorited');
const { favorited, busy, toggle } = usePlantFavoriteToggle(
    props.plantId,
    isFavoritedRef,
);
</script>

<template>
    <PlantFavoriteBadge
        v-if="isAuthenticated"
        :is-favorited="favorited"
        :favorites-count="favoritesCount"
        :interactive="!busy"
        :compact="compact"
        @toggle="toggle"
    />
</template>