<script setup lang="ts">
import type { Paginated } from '@/entities/plant';
import { StoryCreateDialog, StoryDeleteDialog } from '@/features/admin/stories';
import { useAdminFlash } from '@/features/admin/lib';
import { useAppToast } from '@/shared/lib/toast';
import { AppLink } from '@/shared/ui';
import { AdminShell } from '@/widgets/admin';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

type AdminStory = {
    id: number;
    media_url: string;
    caption: string | null;
    active_until: string;
    is_active: boolean;
    visible_on_home: boolean;
    author: { username: string; name: string | null } | null;
};

const props = defineProps<{
    stories: Paginated<AdminStory>;
    filters: { search?: string };
}>();

useAdminFlash();
const toast = useAppToast();

const searchDraft = ref(props.filters.search ?? '');
const createOpen = ref(false);
const storyToDelete = ref<number | null>(null);

function submitSearch(): void {
    const q = searchDraft.value.trim();
    router.get(
        '/admin/stories',
        q !== '' ? { search: q } : {},
        { preserveState: true, preserveScroll: true },
    );
}

function destroyStory(): void {
    const id = storyToDelete.value;
    if (id === null) return;

    router.delete(`/admin/stories/${id}`, {
        preserveScroll: true,
        onSuccess: () => {
            storyToDelete.value = null;
        },
        onError: () => toast.error('Не удалось удалить'),
    });
}

function changeStoryStatus(story: AdminStory, event: Event): void {
    const select = event.target as HTMLSelectElement;
    const status = select.value;

    router.patch(
        `/admin/stories/${story.id}/status`,
        { is_active: status !== 'inactive' },
        {
            preserveScroll: true,
            onError: () => {
                select.value = story.visible_on_home ? 'active' : story.is_active ? 'expired' : 'inactive';
                toast.error('Не удалось изменить статус истории');
            },
        },
    );
}
</script>

<template>
    <AdminShell>
        <div class="section">
            <header class="section__header">
                <h1 class="section__title">Истории</h1>
                <p class="section__subtitle">Активные истории появляются на главной в ленте.</p>
            </header>

            <div class="section__toolbar">
                <form class="search" @submit.prevent="submitSearch">
                    <input
                        v-model="searchDraft"
                        type="search"
                        placeholder="Поиск по подписи или автору…"
                        class="search__input"
                    />
                    <button type="submit" class="search__btn">Найти</button>
                </form>
                <button type="button" class="primary-btn" @click="createOpen = true">
                    + Создать
                </button>
            </div>

            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Превью</th>
                            <th>Подпись / автор</th>
                            <th>Статус</th>
                            <th>До</th>
                            <th />
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="story in stories.data" :key="story.id">
                            <td>
                                <img :src="story.media_url" alt="" class="thumb" />
                            </td>
                            <td>
                                <div class="cell-stack">
                                    <span>{{ story.caption || '—' }}</span>
                                    <span class="muted">
                                        @{{ story.author?.username ?? '?' }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <select
                                    class="status-select"
                                    :value="story.visible_on_home ? 'active' : story.is_active ? 'expired' : 'inactive'"
                                    aria-label="Статус истории"
                                    @change="changeStoryStatus(story, $event)"
                                >
                                    <option value="active">В ленте</option>
                                    <option value="inactive">Выключена</option>
                                    <option v-if="story.is_active && !story.visible_on_home" value="expired" disabled>
                                        Срок истёк
                                    </option>
                                </select>
                            </td>
                            <td class="muted">
                                {{ new Date(story.active_until).toLocaleString('ru-RU') }}
                            </td>
                            <td>
                                <button
                                    type="button"
                                    class="danger-btn"
                                    @click="storyToDelete = story.id"
                                >
                                    Удалить
                                </button>
                            </td>
                        </tr>
                        <tr v-if="stories.data.length === 0">
                            <td colspan="5" class="empty">Нет историй</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <nav v-if="stories.last_page > 1" class="pagination" aria-label="Страницы">
                <AppLink
                    v-for="link in stories.links"
                    :key="`${link.label}-${link.url ?? 'x'}`"
                    :href="link.url ?? '#'"
                    class="page-link"
                    :class="{ 'page-link--active': link.active, 'page-link--disabled': !link.url }"
                >
                    <span v-html="link.label" />
                </AppLink>
            </nav>
        </div>

        <StoryCreateDialog v-if="createOpen" @close="createOpen = false" />
        <StoryDeleteDialog
            v-if="storyToDelete !== null"
            @cancel="storyToDelete = null"
            @confirm="destroyStory"
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

.section__subtitle {
    margin: 0.35rem 0 0;
    font-size: 0.9375rem;
    color: #78928a;
}

.section__toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    align-items: center;
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
    font-size: 0.875rem;
}

.search__btn,
.primary-btn,
.danger-btn {
    border-radius: 0.5rem;
    padding: 0.5rem 0.85rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
}

.search__btn,
.primary-btn {
    border: 1px solid #21483f;
    background: #21483f;
    color: #fff;
}

.danger-btn {
    border: 1px solid #c45c5c;
    background: #fff;
    color: #b33;
}

.status-select {
    min-width: 8.5rem;
    border: 1px solid #7da99a;
    border-radius: 0.5rem;
    padding: 0.45rem 0.6rem;
    background: #f3f8f5;
    color: #21483f;
    font: inherit;
    font-size: 0.8125rem;
    cursor: pointer;
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
    text-align: left;
    border-bottom: 1px solid #e8f0ed;
    vertical-align: middle;
}

.table th {
    font-weight: 600;
    color: #21483f;
    background: #f5f8f7;
}

.thumb {
    width: 3rem;
    height: 3rem;
    object-fit: cover;
    border-radius: 0.5rem;
}

.cell-stack {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}

.muted {
    color: #78928a;
    font-size: 0.8125rem;
}

.badge {
    display: inline-block;
    border-radius: 999px;
    padding: 0.2rem 0.55rem;
    font-size: 0.75rem;
    background: #e8f0ed;
}

.badge--ok {
    background: #21483f;
    color: #fff;
}

.badge--muted {
    background: #f0e8e8;
    color: #955;
}

.empty {
    text-align: center;
    color: #78928a;
    padding: 2rem !important;
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
    font-size: 0.8125rem;
    text-decoration: none;
    color: #21483f;
}

.page-link--active {
    background: #21483f;
    color: #fff;
    border-color: #21483f;
}

.page-link--disabled {
    opacity: 0.45;
    pointer-events: none;
}
</style>
