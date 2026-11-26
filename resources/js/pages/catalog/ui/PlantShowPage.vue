<script setup lang="ts">
import type { PlantShowPageProps } from '@/entities/plant';
import { AppLink } from '@/shared/ui';
import { PlantDetail } from '@/widgets/catalog';
import logoUrl from '@/shared/assets/img/icons/logo.svg';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<Pick<PlantShowPageProps, 'plant'>>();

const page = usePage<PlantShowPageProps>();
const homeHref = computed(() => (page.props.auth.user ? '/' : '/login'));
const homeLabel = computed(() => (page.props.auth.user ? 'На главную' : 'Войти'));
const isAuthenticated = computed(() => page.props.auth.user !== null);
</script>

<template>
    <div class="plant-show-page">
        <div class="plant-show-page__inner">
            <div class="plant-show-page__top">
                <img :src="logoUrl" alt="logo" class="plant-show-page__logo" />
                <nav class="plant-show-page__nav" aria-label="Навигация каталога">
                    <AppLink href="/catalog" class="plant-show-page__nav-link">
                        ← Каталог
                    </AppLink>
                    <AppLink
                        :href="homeHref"
                        class="plant-show-page__nav-link"
                    >
                        {{ homeLabel }}
                    </AppLink>
                </nav>
            </div>
            <PlantDetail
                :plant="props.plant"
                :is-authenticated="isAuthenticated"
            />
        </div>
    </div>
</template>

<style scoped>
.plant-show-page {
    min-height: 100vh;
    background: #f5f8f7;
    padding: 2rem 1.25rem 3rem;
}

.plant-show-page__inner {
    margin: 0 auto;
    max-width: 48rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.plant-show-page__top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.plant-show-page__logo {
    height: 3rem;
    width: auto;
}

.plant-show-page__nav {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 0.75rem 1rem;
}

.plant-show-page__nav-link {
    font-size: 0.875rem;
    color: #21483f;
    text-decoration: underline;
    text-underline-offset: 0.25rem;
}
</style>