<script setup lang="ts">
import type { Paginated } from '@/entities/plant';
import { useAdminFlash } from '@/features/admin/lib';
import { RecipeFormDialog } from '@/features/admin/recipes';
import type { AdminRecipeRow } from '@/features/admin/recipes/ui/RecipeFormDialog.vue';
import { useAppToast } from '@/shared/lib/toast';
import { AppLink } from '@/shared/ui';
import { AdminShell } from '@/widgets/admin';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

type PlantOption = { id: number; name: string };

const props = defineProps<{
    recipes: Paginated<AdminRecipeRow>;
    plantOptions: PlantOption[];
    filters: { search?: string };
}>();

useAdminFlash();
const toast = useAppToast();

const searchDraft = ref(props.filters.search ?? '');
const formOpen = ref(false);
const editing = ref<AdminRecipeRow | null>(null);

type RecipeRow = AdminRecipeRow & {
    plants?: { id: number; name: string }[];
};

function submitSearch(): void {
    router.get('/admin/recipes', searchDraft.value.trim() ? { search: searchDraft.value.trim() } : {});
}

function openCreate(): void {
    editing.value = null;
    formOpen.value = true;
}

function openEdit(recipe: AdminRecipeRow): void {
    editing.value = recipe;
    formOpen.value = true;
}

function destroyRecipe(id: number): void {
    if (!window.confirm('Удалить рецепт?')) {
        return;
    }

    router.delete(`/admin/recipes/${id}`, {
        preserveScroll: true,
        onError: () => toast.error('Не удалось удалить'),
    });
}
</script>

<template>
    <AdminShell>
        <div class="section">
            <h1 class="section__title">Рецепты</h1>

            <div class="section__toolbar">
                <form class="search" @submit.prevent="submitSearch">
                    <input v-model="searchDraft" type="search" placeholder="Поиск…" class="search__input" />
                    <button type="submit" class="search__btn">Найти</button>
                </form>
                <button type="button" class="primary-btn" @click="openCreate">+ Создать</button>
            </div>

            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Название</th>
                            <th>Растения</th>
                            <th />
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="recipe in recipes.data as RecipeRow[]" :key="recipe.id">
                            <td>
                                <div>{{ recipe.name }}</div>
                                <div class="muted truncate">{{ recipe.description ?? '—' }}</div>
                            </td>
                            <td class="muted">
                                {{ recipe.plants?.map((p) => p.name).join(', ') || '—' }}
                            </td>
                            <td class="actions">
                                <button type="button" class="ghost-btn" @click="openEdit(recipe)">Изменить</button>
                                <button type="button" class="danger-btn" @click="destroyRecipe(recipe.id)">
                                    Удалить
                                </button>
                            </td>
                        </tr>
                        <tr v-if="recipes.data.length === 0">
                            <td colspan="3" class="empty">Нет рецептов</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <nav v-if="recipes.last_page > 1" class="pagination">
                <AppLink
                    v-for="link in recipes.links"
                    :key="`${link.label}-${link.url ?? 'x'}`"
                    :href="link.url ?? '#'"
                    class="page-link"
                    :class="{ 'page-link--active': link.active }"
                >
                    <span v-html="link.label" />
                </AppLink>
            </nav>
        </div>

        <RecipeFormDialog
            v-if="formOpen"
            :plant-options="plantOptions"
            :recipe="editing"
            @close="formOpen = false"
        />
    </AdminShell>
</template>

<style scoped>
.section {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    max-width: 56rem;
}

.section__title {
    margin: 0;
    font-size: 1.625rem;
}

.section__toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.search {
    display: flex;
    flex: 1;
    gap: 0.5rem;
}

.search__input {
    flex: 1;
    border-radius: 0.5rem;
    border: 1px solid #d7e5e0;
    padding: 0.5rem;
}

.search__btn,
.primary-btn,
.ghost-btn,
.danger-btn {
    border-radius: 0.5rem;
    padding: 0.45rem 0.75rem;
    font-size: 0.875rem;
    cursor: pointer;
}

.search__btn,
.primary-btn {
    background: #21483f;
    color: #fff;
    border: 1px solid #21483f;
}

.ghost-btn {
    border: 1px solid #d7e5e0;
    background: #fff;
}

.danger-btn {
    border: 1px solid #c45c5c;
    color: #b33;
    background: #fff;
}

.table-wrap {
    border: 1px solid #d7e5e0;
    border-radius: 0.75rem;
    background: #fff;
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.table th,
.table td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #e8f0ed;
    text-align: left;
}

.table th {
    background: #f5f8f7;
}

.muted {
    color: #78928a;
    font-size: 0.8125rem;
}

.truncate {
    max-width: 22rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.actions {
    display: flex;
    gap: 0.35rem;
}

.empty {
    text-align: center;
    color: #78928a;
}

.pagination {
    display: flex;
    gap: 0.35rem;
}

.page-link {
    padding: 0.35rem 0.65rem;
    border: 1px solid #d7e5e0;
    border-radius: 0.4rem;
    text-decoration: none;
    color: #21483f;
    font-size: 0.8125rem;
}

.page-link--active {
    background: #21483f;
    color: #fff;
}
</style>