<script setup lang="ts">
import type { PlantShowPageProps } from '@/entities/plant';
import { PlantDetail } from '@/widgets/catalog';
import { AppShell } from '@/widgets/layout';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<Pick<PlantShowPageProps, 'plant'>>();

const page = usePage<PlantShowPageProps>();
const isAuthenticated = computed(() => page.props.auth.user !== null);
</script>

<template>
    <AppShell max-width-class="max-w-3xl">
        <PlantDetail
            :plant="props.plant"
            :is-authenticated="isAuthenticated"
        />
    </AppShell>
</template>