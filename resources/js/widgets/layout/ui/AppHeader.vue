<script setup lang="ts">
import type { AuthPageProps, AuthUser } from '@/entities/user';
import logoUrl from '@/shared/assets/img/icons/logo.svg';
import { AppLink } from '@/shared/ui';
import { router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const defaultAvatarUrl = '/image/avatar-loading.svg';

const page = usePage<AuthPageProps & { filters?: { search?: string } }>();

const authUser = computed(() => page.props.auth.user);

const searchDraft = ref('');

watch(
    () => page.props.filters?.search ?? '',
    (value) => {
        searchDraft.value = value;
    },
    { immediate: true },
);

const avatarSrc = computed(() => {
    const user = authUser.value;
    if (!user) {
        return defaultAvatarUrl;
    }

    return user.avatar?.trim() || defaultAvatarUrl;
});

const showAvatarImage = computed(() => {
    const user = authUser.value;
    return Boolean(user?.avatar?.trim());
});

function userInitials(user: AuthUser): string {
    const source = user.name?.trim() || user.username.trim();
    const parts = source.split(/\s+/).filter(Boolean);

    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }

    return source.slice(0, 2).toUpperCase();
}

const accountInitials = computed(() => {
    const user = authUser.value;
    return user ? userInitials(user) : '';
});

const logoHref = computed(() => (authUser.value ? '/' : '/catalog'));

function submitSearch(): void {
    const query = searchDraft.value.trim();

    router.get(
        '/catalog',
        query !== '' ? { search: query } : {},
        { preserveState: true, preserveScroll: true },
    );
}
</script>

<template>
    <header
        class="sticky top-0 z-50 border-b border-[#d7e5e0] bg-white/95 backdrop-blur-sm"
    >
        <div
            class="mx-auto flex w-full max-w-6xl flex-wrap items-center gap-3 gap-y-2 px-5 py-3"
        >
            <AppLink
                :href="logoHref"
                class="shrink-0 rounded-md focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#21483f]"
            >
                <img :src="logoUrl" alt="elfi_ros" class="h-10 w-auto md:h-11" />
            </AppLink>

            <nav
                class="flex flex-wrap items-center gap-3 text-sm font-medium text-[#21483f]"
                aria-label="Основная навигация"
            >
                <AppLink
                    href="/catalog"
                    class="rounded-md px-2 py-1 transition-colors hover:bg-[#f0f5f3] hover:text-[#203f39]"
                >
                    Каталог
                </AppLink>
                <AppLink
                    v-if="authUser"
                    href="/"
                    class="rounded-md px-2 py-1 transition-colors hover:bg-[#f0f5f3] hover:text-[#203f39]"
                >
                    Главная
                </AppLink>
            </nav>

            <form
                class="order-3 flex min-w-[12rem] flex-1 basis-full items-center gap-2 sm:order-none sm:basis-[14rem] md:min-w-[16rem]"
                role="search"
                @submit.prevent="submitSearch"
            >
                <label class="sr-only" for="app-header-search">Поиск растений</label>
                <input
                    id="app-header-search"
                    v-model="searchDraft"
                    type="search"
                    name="search"
                    placeholder="Поиск в каталоге…"
                    autocomplete="off"
                    class="w-full rounded-lg border border-[#d7e5e0] bg-[#f5f8f7] px-3 py-2 text-sm text-[#203f39] placeholder:text-[#78928a] focus:border-[#21483f] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#21483f]/20"
                />
                <button
                    type="submit"
                    class="shrink-0 rounded-lg border border-[#21483f] bg-[#21483f] px-3 py-2 text-sm font-medium text-white transition-colors hover:bg-[#1a3a32] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#21483f]"
                >
                    Найти
                </button>
            </form>

            <AppLink
                href="/account/favorites"
                class="order-2 ml-auto shrink-0 text-sm font-medium text-[#21483f] underline decoration-[#21483f]/35 underline-offset-4 transition-colors hover:decoration-[#21483f] sm:order-none sm:ml-0"
            >
                Избранное
            </AppLink>

            <div class="order-4 flex shrink-0 items-center sm:order-none">
                <AppLink
                    v-if="authUser"
                    href="/account"
                    class="flex items-center gap-2 rounded-full p-0.5 transition-opacity hover:opacity-90 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#21483f]"
                    :title="authUser.username"
                >
                    <img
                        v-if="showAvatarImage"
                        :src="avatarSrc"
                        alt=""
                        class="h-9 w-9 rounded-full border-2 border-[#21483f] object-cover"
                    />
                    <span
                        v-else
                        class="flex h-9 w-9 items-center justify-center rounded-full border-2 border-[#21483f] bg-[#e8f0ed] text-xs font-semibold text-[#21483f]"
                        aria-hidden="true"
                    >
                        {{ accountInitials }}
                    </span>
                    <span class="hidden text-sm text-[#203f39] md:inline">
                        Кабинет
                    </span>
                </AppLink>
                <AppLink
                    v-else
                    href="/login"
                    class="rounded-lg border border-[#21483f] px-3 py-1.5 text-sm font-medium text-[#21483f] transition-colors hover:bg-[#f0f5f3]"
                >
                    Войти
                </AppLink>
            </div>
        </div>
    </header>
</template>