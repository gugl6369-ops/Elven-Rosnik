<script setup lang="ts">
import type { Paginated, PlantListItem } from '@/entities/plant';
import { PlantCard } from '@/entities/plant';
import { AppLink } from '@/shared/ui';

defineProps<{
    plants: Paginated<PlantListItem>;
}>();
</script>

<template>
    <div class="catalog">
        <header class="catalog__header">
            <h1 class="catalog__title">Каталог растений</h1>
            <p class="catalog__subtitle">Только просмотр — карточки из схемы `app.plant`.</p>
        </header>

        <div v-if="plants.data.length === 0" class="catalog__empty">
            <p>В каталоге пока нет растений.</p>
            <p class="catalog__empty-hint">
                Добавьте записи в БД или выполните
                <code>php artisan db:seed --class=PlantCatalogSeeder</code>.
            </p>
        </div>

        <ul v-else class="catalog__grid">
            <li v-for="plant in plants.data" :key="plant.id">
                <AppLink
                    :href="`/catalog/plants/${plant.id}`"
                    class="catalog__card-link"
                >
                    <PlantCard :plant="plant" />
                </AppLink>
            </li>
        </ul>

        <nav
            v-if="plants.last_page > 1"
            class="catalog__pagination"
            aria-label="Страницы каталога"
        >
            <AppLink
                v-for="link in plants.links"
                :key="`${link.label}-${link.url ?? 'null'}`"
                :href="link.url ?? '#'"
                class="catalog__page-link"
                :class="{
                    'catalog__page-link--active': link.active,
                    'catalog__page-link--disabled': link.url === null,
                }"
                :aria-disabled="link.url === null ? 'true' : undefined"
            >
                <span v-html="link.label" />
            </AppLink>
        </nav>
    </div>
</template>

<style scoped>
.catalog {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.catalog__header {
    text-align: center;
}

.catalog__title {
    margin: 0;
    font-size: 2rem;
    font-weight: 600;
    color: #203f39;
}

.catalog__subtitle {
    margin: 0.5rem 0 0;
    font-size: 0.9375rem;
    color: #78928a;
}

.catalog__empty {
    text-align: center;
    padding: 2.5rem 1rem;
    border-radius: 1rem;
    border: 1px dashed #b8cdc6;
    background: #fff;
    color: #203f39;
}

.catalog__empty-hint {
    margin: 0.75rem 0 0;
    font-size: 0.875rem;
    color: #5a746c;
}

.catalog__empty code {
    font-size: 0.8125rem;
}

.catalog__grid {
    margin: 0;
    padding: 0;
    list-style: none;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(16rem, 1fr));
    gap: 1.25rem;
}

.catalog__card-link {
    display: block;
    height: 100%;
    color: inherit;
    text-decoration: none;
    border-radius: 1rem;
    transition: box-shadow 0.15s ease, transform 0.15s ease;
}

.catalog__card-link:hover {
    box-shadow: 0 6px 20px rgb(32 63 57 / 12%);
    transform: translateY(-2px);
}

.catalog__card-link:focus-visible {
    outline: 2px solid #21483f;
    outline-offset: 3px;
}

.catalog__pagination {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.35rem;
}

.catalog__page-link {
    display: inline-flex;
    min-width: 2.25rem;
    justify-content: center;
    padding: 0.35rem 0.6rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    text-decoration: none;
    color: #21483f;
    border: 1px solid #d7e5e0;
    background: #fff;
}

.catalog__page-link--active {
    background: #21483f;
    border-color: #21483f;
    color: #fff;
}

.catalog__page-link--disabled {
    opacity: 0.45;
    pointer-events: none;
}
</style>