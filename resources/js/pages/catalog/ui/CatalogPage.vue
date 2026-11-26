<script setup lang="ts">
import type { CatalogPageProps } from '@/entities/plant';
import { AppLink } from '@/shared/ui';
import { Catalog } from '@/widgets/catalog';
import logoUrl from '@/shared/assets/img/icons/logo.svg';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<Pick<CatalogPageProps, 'plants'>>();

const page = usePage<CatalogPageProps>();
const homeHref = computed(() => (page.props.auth.user ? '/' : '/login'));
const homeLabel = computed(() => (page.props.auth.user ? 'На главную' : 'Войти'));
</script>

<template>
    <div class="catalog-page">
        <div class="catalog-page__inner">
            <div class="catalog-page__top">
                <img :src="logoUrl" alt="logo" class="catalog-page__logo" />
                <AppLink
                    :href="homeHref"
                    class="catalog-page__home-link"
                >
                    {{ homeLabel }}
                </AppLink>
            </div>
            <Catalog :plants="props.plants" />
        </div>
    </div>
</template>

<style scoped>
.catalog-page {
    min-height: 100vh;
    background: #f5f8f7;
    padding: 2rem 1.25rem 3rem;
}

.catalog-page__inner {
    margin: 0 auto;
    max-width: 72rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.catalog-page__top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.catalog-page__logo {
    height: 3rem;
    width: auto;
}

.catalog-page__home-link {
    font-size: 0.875rem;
    color: #21483f;
    text-decoration: underline;
    text-underline-offset: 0.25rem;
}
</style>