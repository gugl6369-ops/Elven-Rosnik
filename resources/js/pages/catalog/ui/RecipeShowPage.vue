<script setup lang="ts">
import type { RecipeShowPageProps } from '@/entities/recipe';
import { RecipeDetail } from '@/widgets/catalog';
import { AppShell } from '@/widgets/layout';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<Pick<RecipeShowPageProps, 'recipe'>>();

const page = usePage<RecipeShowPageProps>();
const isAuthenticated = computed(() => page.props.auth.user !== null);
const currentUserId = computed(() => page.props.auth.user?.id ?? null);
const isAdmin = computed(() => page.props.auth.user?.is_admin === true);
</script>

<template>
    <AppShell max-width-class="max-w-3xl">
        <nav class="recipe-show__back">
            <Link href="/catalog" class="recipe-show__back-link">
                ← Каталог
            </Link>
        </nav>
        <RecipeDetail
            :recipe="props.recipe"
            :is-authenticated="isAuthenticated"
            :current-user-id="currentUserId"
            :is-admin="isAdmin"
        />
    </AppShell>
</template>

<style scoped>
.recipe-show__back {
    margin-bottom: 1rem;
}

.recipe-show__back-link {
    font-size: 0.875rem;
    color: #2f6d61;
    text-decoration: underline;
}
</style>