<script setup lang="ts">
import type { CatalogPageProps } from '@/entities/plant';
import { Catalog } from '@/widgets/catalog';
import { AppShell } from '@/widgets/layout';
import { computed } from 'vue';

const props = defineProps<Pick<CatalogPageProps, 'plants' | 'filters'>>();

const catalogSubtitle = computed(() => {
    const query = props.filters?.search?.trim();

    if (query) {
        return `Результаты по запросу «${query}».`;
    }

    return 'Только просмотр — карточки из схемы `app.plant`.';
});
</script>

<template>
    <AppShell>
        <Catalog :plants="props.plants" :subtitle="catalogSubtitle" />
    </AppShell>
</template>