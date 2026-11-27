<script setup lang="ts">
import type { FavoritesPageProps } from '@/entities/plant';
import { Catalog } from '@/widgets/catalog';
import { AppShell } from '@/widgets/layout';

const props = defineProps<Pick<FavoritesPageProps, 'plants'>>();
</script>

<template>
    <AppShell>
        <Catalog
            :plants="props.plants"
            title="Избранное"
            subtitle="Растения, которые вы сохранили в избранное."
            empty-title="В избранном пока нет растений."
            empty-hint="Откройте каталог и добавьте растения в избранное с карточки растения."
        />
    </AppShell>
</template>