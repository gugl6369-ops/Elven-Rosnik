<script setup lang="ts">
import type { AuthUser } from '@/entities/user';
import { LogoutButton } from '@/features/account/logout';
import { ViewProfile } from '@/features/account/view-profile';
import logoUrl from '@/shared/assets/img/icons/logo.svg';
import { AppLink } from '@/shared/ui';

defineProps<{
    user: AuthUser;
}>();
</script>

<template>
    <div class="min-h-screen w-full bg-[#f5f8f7]">
        <div class="mx-auto flex w-full max-w-xl flex-col gap-8 px-6 py-12">
            <div class="flex flex-col items-center gap-4">
                <img :src="logoUrl" alt="logo" class="h-16 w-auto" />
                <h1 class="text-center text-3xl text-[#203f39]">
                    Личный кабинет
                </h1>
                <p class="text-center text-sm text-[#78928a]">
                    Данные профиля (только просмотр)
                </p>
            </div>
            <ViewProfile :user="user" />
            <LogoutButton />
            <div class="flex justify-center">
                <AppLink
                    href="/"
                    class="text-sm text-[#21483f] underline decoration-[#21483f]/40 underline-offset-4 hover:decoration-[#21483f]"
                >
                    На главную
                </AppLink>
            </div>
        </div>
    </div>
</template>