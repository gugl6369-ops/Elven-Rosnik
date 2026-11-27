<script setup lang="ts">
import type { FavoritesPageProps } from '@/entities/plant';
import { AppLink } from '@/shared/ui';
import { Catalog } from '@/widgets/catalog';
import logoUrl from '@/shared/assets/img/icons/logo.svg';

const props = defineProps<Pick<FavoritesPageProps, 'plants'>>();
</script>

<template>
    <div class="favorites-page">
        <div class="favorites-page__inner">
            <div class="favorites-page__top">
                <img :src="logoUrl" alt="logo" class="favorites-page__logo" />
                <nav class="favorites-page__nav" aria-label="Навигация">
                    <AppLink href="/account" class="favorites-page__link">
                        Личный кабинет
                    </AppLink>
                    <AppLink href="/catalog" class="favorites-page__link">
                        Каталог
                    </AppLink>
                </nav>
            </div>
            <Catalog
                :plants="props.plants"
                title="Избранное"
                subtitle="Растения, которые вы сохранили в избранное."
                empty-title="В избранном пока нет растений."
                empty-hint="Откройте каталог и добавьте растения в избранное с карточки растения."
            />
        </div>
    </div>
</template>

<style scoped>
.favorites-page {
    min-height: 100vh;
    background: #f5f8f7;
    padding: 2rem 1.25rem 3rem;
}

.favorites-page__inner {
    margin: 0 auto;
    max-width: 72rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.favorites-page__top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}

.favorites-page__logo {
    height: 3rem;
    width: auto;
}

.favorites-page__nav {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

.favorites-page__link {
    font-size: 0.875rem;
    color: #21483f;
    text-decoration: underline;
    text-underline-offset: 0.25rem;
}
</style>