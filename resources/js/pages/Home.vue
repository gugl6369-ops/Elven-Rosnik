<script setup lang="ts">
import type { AuthPageProps } from '@/entities/user';
import { LogoutButton } from '@/features/account/logout';
import { AppLink } from '@/shared/ui';
import { AppShell } from '@/widgets/layout';
import { usePage } from '@inertiajs/vue3';

defineProps<{
    appName?: string;
}>();

const page = usePage<AuthPageProps>();
</script>

<template>
    <AppShell max-width-class="max-w-xl">
        <div class="flex flex-col items-center gap-6 text-center">
            <div class="flex flex-col gap-2">
                <h1 class="m-0 text-4xl font-semibold tracking-tight text-[#203f39]">
                    {{ appName ?? 'elfi_ros' }}
                </h1>
                <p class="m-0 text-sm text-[#78928a]">
                    Добро пожаловать в каталог растений.
                </p>
            </div>
            <div
                v-if="page.props.auth.user"
                class="flex w-full max-w-sm flex-col items-stretch gap-3"
            >
                <AppLink
                    href="/catalog"
                    class="rounded-xl border border-[#21483f] bg-[#21483f] px-4 py-3 text-center text-sm font-medium text-white transition-colors hover:bg-[#1a3a32]"
                >
                    Каталог растений
                </AppLink>
                <AppLink
                    href="/account"
                    class="rounded-xl border border-[#d7e5e0] bg-white px-4 py-3 text-center text-sm font-medium text-[#21483f] transition-colors hover:bg-[#f0f5f3]"
                >
                    Личный кабинет
                </AppLink>
                <LogoutButton />
            </div>
        </div>
    </AppShell>
</template>