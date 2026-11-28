<script setup lang="ts">
import type { Paginated } from '@/entities/plant';
import type { AuthUser } from '@/entities/user';
import { useAdminFlash } from '@/features/admin/lib';
import { useAppToast } from '@/shared/lib/toast';
import { AppLink } from '@/shared/ui';
import { AdminShell } from '@/widgets/admin';
import { router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

type AdminUserRow = AuthUser & {
    is_blocked?: boolean;
};

const props = defineProps<{
    users: Paginated<AdminUserRow>;
    filters: { search?: string };
}>();

useAdminFlash();
const toast = useAppToast();
const page = usePage();

const authUser = computed(() => page.props.auth?.user ?? null);
const searchDraft = ref(props.filters.search ?? '');

function submitSearch(): void {
    const q = searchDraft.value.trim();
    router.get('/admin/users', q !== '' ? { search: q } : {}, { preserveState: true });
}

function canModify(target: AdminUserRow): boolean {
    return authUser.value != null && authUser.value.id !== target.id;
}

function canToggleAdmin(target: AdminUserRow): boolean {
    return canModify(target) && (target.is_admin || target.is_blocked !== true);
}

function canBlock(target: AdminUserRow): boolean {
    return canModify(target) && (!target.is_admin || target.is_blocked === true);
}

function toggleAdmin(target: AdminUserRow): void {
    if (!canModify(target)) {
        toast.error('Нельзя изменить себя');
        return;
    }

    if (!target.is_admin && target.is_blocked) {
        toast.error('Сначала разблокируйте пользователя');
        return;
    }

    router.patch(
        `/admin/users/${target.id}/admin`,
        {},
        {
            preserveScroll: true,
            onError: () => toast.error('Не удалось обновить роль'),
        },
    );
}

function toggleBlock(target: AdminUserRow): void {
    if (!canBlock(target)) {
        toast.error('Нельзя блокировать этого пользователя');
        return;
    }

    router.patch(
        `/admin/users/${target.id}/block`,
        {},
        {
            preserveScroll: true,
            onError: () => toast.error('Не удалось изменить блокировку'),
        },
    );
}
</script>

<template>
    <AdminShell>
        <div class="section">
            <h1 class="section__title">Пользователи</h1>

            <form class="search" @submit.prevent="submitSearch">
                <input
                    v-model="searchDraft"
                    type="search"
                    placeholder="Поиск: username, email, имя…"
                    class="search__input"
                />
                <button type="submit" class="search__btn">Найти</button>
            </form>

            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Пользователь</th>
                            <th>Email</th>
                            <th>Роли</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users.data" :key="user.id">
                            <td>
                                <div class="cell-stack">
                                    <span>@{{ user.username }}</span>
                                    <span class="muted">{{ user.name ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="muted">{{ user.email ?? '—' }}</td>
                            <td>
                                <span v-if="user.is_admin" class="badge badge--admin">Админ</span>
                                <span v-if="user.is_blocked" class="badge badge--blocked">Заблокирован</span>
                                <span v-if="!user.is_admin && !user.is_blocked" class="muted">—</span>
                            </td>
                            <td class="actions">
                                <button
                                    type="button"
                                    class="ghost-btn"
                                    :disabled="!canToggleAdmin(user)"
                                    :title="
                                        user.is_blocked && !user.is_admin
                                            ? 'Сначала разблокируйте пользователя'
                                            : undefined
                                    "
                                    @click="toggleAdmin(user)"
                                >
                                    {{ user.is_admin ? 'Снять админ' : 'Сделать админом' }}
                                </button>
                                <button
                                    type="button"
                                    class="ghost-btn"
                                    :disabled="!canBlock(user)"
                                    @click="toggleBlock(user)"
                                >
                                    {{ user.is_blocked ? 'Разблокировать' : 'Заблокировать' }}
                                </button>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td colspan="4" class="empty">Нет пользователей</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <nav v-if="users.last_page > 1" class="pagination">
                <AppLink
                    v-for="link in users.links"
                    :key="`${link.label}-${link.url ?? 'x'}`"
                    :href="link.url ?? '#'"
                    class="page-link"
                    :class="{ 'page-link--active': link.active }"
                >
                    <span v-html="link.label" />
                </AppLink>
            </nav>
        </div>
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

.search {
    display: flex;
    gap: 0.5rem;
}

.search__input {
    flex: 1;
    border-radius: 0.5rem;
    border: 1px solid #d7e5e0;
    padding: 0.5rem 0.65rem;
}

.search__btn {
    border-radius: 0.5rem;
    border: 1px solid #21483f;
    background: #21483f;
    color: #fff;
    padding: 0.45rem 0.85rem;
    cursor: pointer;
}

.table-wrap {
    border-radius: 0.75rem;
    border: 1px solid #d7e5e0;
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
    vertical-align: middle;
}

.table th {
    background: #f5f8f7;
    font-weight: 600;
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
    margin-right: 0.35rem;
    border-radius: 999px;
    padding: 0.2rem 0.5rem;
    font-size: 0.75rem;
}

.badge--admin {
    background: #21483f;
    color: #fff;
}

.badge--blocked {
    background: #f0e0e0;
    color: #933;
}

.actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.35rem;
}

.ghost-btn {
    border-radius: 0.5rem;
    border: 1px solid #d7e5e0;
    background: #fff;
    padding: 0.35rem 0.6rem;
    font-size: 0.8125rem;
    cursor: pointer;
    color: #21483f;
}

.ghost-btn:disabled {
    opacity: 0.45;
    cursor: not-allowed;
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
