<script setup lang="ts">
import type { Paginated, PlantGenus, PlantListItem } from '@/entities/plant';
import { useAdminFlash } from '@/features/admin/lib';
import { PlantFormDialog } from '@/features/admin/plants';
import { useAppToast } from '@/shared/lib/toast';
import { AppLink } from '@/shared/ui';
import { AdminShell } from '@/widgets/admin';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

type TagOption = { id: number; name: string; category: string };

type AdminPlant = PlantListItem & {
    tag_ids: number[];
};

const props = defineProps<{
    plants: Paginated<AdminPlant>;
    genera: PlantGenus[];
    tags: TagOption[];
    filters: { search?: string };
}>();

useAdminFlash();
const toast = useAppToast();

const searchDraft = ref(props.filters.search ?? '');
const formOpen = ref(false);
const editingPlant = ref<AdminPlant | null>(null);

function submitSearch(): void {
    const q = searchDraft.value.trim();
    router.get('/admin/plants', q !== '' ? { search: q } : {}, { preserveState: true });
}

function openCreate(): void {
    editingPlant.value = null;
    formOpen.value = true;
}

function openEdit(plant: AdminPlant): void {
    editingPlant.value = plant;
    formOpen.value = true;
}

function closeForm(): void {
    formOpen.value = false;
    editingPlant.value = null;
}

function destroyPlant(id: number): void {
    if (!window.confirm('Удалить карточку?')) {
        return;
    }

    router.delete(`/admin/plants/${id}`, {
        preserveScroll: true,
        onError: () => toast.error('Не удалось удалить'),
    });
}
</script>

<template>
    <AdminShell>
        <div class="section">
            <header>
                <h1 class="section__title">Карточки растений</h1>
            </header>

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
                            <th>Род</th>
                            <th>Описание</th>
                            <th />
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="plant in plants.data" :key="plant.id">
                            <td>{{ plant.name }}</td>
                            <td>{{ plant.genus?.genus_name ?? '—' }}</td>
                            <td class="muted truncate">{{ plant.description ?? '—' }}</td>
                            <td class="actions">
                                <button type="button" class="ghost-btn" @click="openEdit(plant)">Изменить</button>
                                <button type="button" class="danger-btn" @click="destroyPlant(plant.id)">
                                    Удалить
                                </button>
                            </td>
                        </tr>
                        <tr v-if="plants.data.length === 0">
                            <td colspan="4" class="empty">Нет карточек</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <nav v-if="plants.last_page > 1" class="pagination">
                <AppLink
                    v-for="link in plants.links"
                    :key="`${link.label}-${link.url ?? 'x'}`"
                    :href="link.url ?? '#'"
                    class="page-link"
                    :class="{ 'page-link--active': link.active, 'page-link--disabled': !link.url }"
                >
                    <span v-html="link.label" />
                </AppLink>
            </nav>
        </div>

        <PlantFormDialog
            v-if="formOpen"
            :genera="genera"
            :tags="tags"
            :plant="editingPlant"
            @close="closeForm"
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
    font-weight: 600;
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
    min-width: 12rem;
}

.search__input {
    flex: 1;
    border-radius: 0.5rem;
    border: 1px solid #d7e5e0;
    padding: 0.5rem 0.65rem;
}

.search__btn,
.primary-btn,
.danger-btn,
.ghost-btn {
    border-radius: 0.5rem;
    padding: 0.45rem 0.75rem;
    font-size: 0.875rem;
    cursor: pointer;
}

.search__btn,
.primary-btn {
    border: 1px solid #21483f;
    background: #21483f;
    color: #fff;
}

.ghost-btn {
    border: 1px solid #d7e5e0;
    background: #fff;
    color: #21483f;
}

.danger-btn {
    border: 1px solid #c45c5c;
    background: #fff;
    color: #b33;
}

.table-wrap {
    overflow-x: auto;
    border-radius: 0.75rem;
    border: 1px solid #d7e5e0;
    background: #fff;
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
    font-weight: 600;
}

.muted {
    color: #78928a;
}

.truncate {
    max-width: 18rem;
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
    flex-wrap: wrap;
    gap: 0.35rem;
}

.page-link {
    padding: 0.35rem 0.65rem;
    border-radius: 0.4rem;
    border: 1px solid #d7e5e0;
    text-decoration: none;
    color: #21483f;
    font-size: 0.8125rem;
}

.page-link--active {
    background: #21483f;
    color: #fff;
}
</style>