<script setup lang="ts">
import { computed } from 'vue';

import type { AuthUser } from '@/entities/user';
import { ChangeAvatar } from '@/features/account/change-avatar';
import { AppInput } from '@/shared/ui';

const props = defineProps<{
    user: AuthUser;
}>();

const memberSince = computed(() => {
    if (!props.user.created_at) {
        return '—';
    }

    const date = new Date(props.user.created_at);

    if (Number.isNaN(date.getTime())) {
        return props.user.created_at;
    }

    return new Intl.DateTimeFormat('ru-RU', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(date);
});

const display = (value: string | null | undefined): string =>
    value?.trim() ? value.trim() : '—';
</script>

<template>
    <section class="flex w-full flex-col gap-8">
        <ChangeAvatar :user="user" />
        <form class="flex w-full flex-col gap-5" @submit.prevent>
            <AppInput
                :model-value="display(user.username)"
                disabled
                label="Имя пользователя"
                placeholder=""
            />
            <AppInput
                :model-value="display(user.name)"
                disabled
                label="Имя"
                placeholder=""
            />
            <AppInput
                :model-value="display(user.email)"
                disabled
                label="Почта"
                placeholder=""
            />
            <AppInput
                :model-value="display(user.status)"
                disabled
                label="Статус"
                placeholder=""
            />
            <AppInput
                :model-value="memberSince"
                disabled
                label="В роснике с"
                placeholder=""
            />
        </form>
    </section>
</template>