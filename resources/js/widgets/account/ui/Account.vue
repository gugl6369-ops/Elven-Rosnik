<script setup lang="ts">
import type { AuthUser } from '@/entities/user';
import { LogoutButton } from '@/features/account/logout';
import { ViewProfile } from '@/features/account/view-profile';
import { AppShell } from '@/widgets/layout';

defineProps<{
    user: AuthUser;
}>();
</script>

<template>
    <AppShell max-width-class="max-w-xl">
        <div class="flex flex-col items-center gap-8">
            <div class="flex flex-col items-center gap-2">
                <h1 class="text-center text-3xl text-[#203f39]">
                    Личный кабинет
                </h1>
                <p class="text-center text-sm text-[#78928a]">
                    Аватар можно изменить; остальные поля только для просмотра
                </p>
            </div>
            <ViewProfile :user="user" />
            <LogoutButton />
        </div>
    </AppShell>
</template>