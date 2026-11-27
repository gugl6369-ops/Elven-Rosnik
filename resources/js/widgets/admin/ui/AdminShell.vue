<script setup lang="ts">
import logoUrl from '@/shared/assets/img/icons/logo.svg';
import { AppLink } from '@/shared/ui';
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const sidebarOpen = ref(true);

const navItems = [
    { href: '/admin/stories', label: 'Истории', prefix: '/admin/stories' },
    { href: '/admin/plants', label: 'Карточки', prefix: '/admin/plants' },
    { href: '/admin/recipes', label: 'Рецепты', prefix: '/admin/recipes' },
    { href: '/admin/users', label: 'Пользователи', prefix: '/admin/users' },
] as const;

const currentPath = computed(() => page.url.split('?')[0] ?? '');

function isActive(prefix: string): boolean {
    return currentPath.value === prefix || currentPath.value.startsWith(`${prefix}/`);
}

function toggleSidebar(): void {
    sidebarOpen.value = !sidebarOpen.value;
}
</script>

<template>
    <div class="admin-root">
        <button
            type="button"
            class="admin-root__toggle md:hidden"
            aria-label="Меню админки"
            @click="toggleSidebar"
        >
            ☰
        </button>

        <div class="admin-root__layout" :class="{ 'admin-root__layout--nav-hidden': !sidebarOpen }">
            <aside class="admin-sidebar" :class="{ 'admin-sidebar--hidden': !sidebarOpen }">
                <div class="admin-sidebar__brand">
                    <AppLink href="/" class="admin-sidebar__logo-link">
                        <img :src="logoUrl" alt="elfi_ros" class="admin-sidebar__logo" />
                    </AppLink>
                    <p class="admin-sidebar__title">Админ-панель</p>
                </div>

                <nav class="admin-sidebar__nav" aria-label="Разделы админки">
                    <AppLink
                        v-for="item in navItems"
                        :key="item.href"
                        :href="item.href"
                        class="admin-sidebar__link"
                        :class="{ 'admin-sidebar__link--active': isActive(item.prefix) }"
                    >
                        {{ item.label }}
                    </AppLink>
                </nav>

                <AppLink href="/" class="admin-sidebar__back">← На сайт</AppLink>
            </aside>

            <main class="admin-main">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.admin-root {
    min-height: 100vh;
    background: #f5f8f7;
    color: #203f39;
}

.admin-root__toggle {
    position: fixed;
    top: 0.75rem;
    left: 0.75rem;
    z-index: 40;
    border: 1px solid #d7e5e0;
    border-radius: 0.5rem;
    background: #fff;
    padding: 0.35rem 0.65rem;
    font-size: 1.125rem;
    color: #21483f;
}

.admin-root__layout {
    display: flex;
    min-height: 100vh;
}

.admin-sidebar {
    width: 14.5rem;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    border-right: 1px solid #d7e5e0;
    background: #fff;
    padding: 1.25rem 1rem 1.5rem;
}

.admin-sidebar--hidden {
    display: none;
}

@media (min-width: 768px) {
    .admin-sidebar--hidden {
        display: flex;
    }

    .admin-root__toggle {
        display: none;
    }
}

.admin-sidebar__brand {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    padding: 0 0.25rem;
}

.admin-sidebar__logo {
    height: 2.25rem;
    width: auto;
}

.admin-sidebar__title {
    margin: 0;
    font-size: 0.8125rem;
    font-weight: 600;
    color: #78928a;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}

.admin-sidebar__nav {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}

.admin-sidebar__link {
    border-radius: 0.625rem;
    padding: 0.55rem 0.75rem;
    font-size: 0.9375rem;
    font-weight: 500;
    color: #21483f;
    text-decoration: none;
    transition: background-color 0.15s ease;
}

.admin-sidebar__link:hover {
    background: #f0f5f3;
}

.admin-sidebar__link--active {
    background: #21483f;
    color: #fff;
}

.admin-sidebar__back {
    margin-top: auto;
    font-size: 0.875rem;
    color: #78928a;
    text-decoration: none;
}

.admin-sidebar__back:hover {
    color: #21483f;
}

.admin-main {
    flex: 1;
    min-width: 0;
    padding: 1.5rem 1.25rem 2.5rem;
}

@media (min-width: 768px) {
    .admin-main {
        padding: 2rem 2rem 3rem;
    }
}
</style>