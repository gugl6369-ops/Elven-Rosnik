<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';

import type { AuthUser } from '@/entities/user';
import { useAppToast } from '@/shared/lib/toast';
import { AppInput, Button } from '@/shared/ui';

const props = defineProps<{ user: AuthUser }>();
const toast = useAppToast();

const profile = useForm({
    username: props.user.username,
    name: props.user.name ?? '',
});

const password = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

watch(
    () => [props.user.username, props.user.name] as const,
    ([username, name]) => {
        profile.username = username;
        profile.name = name ?? '';
    },
    { immediate: true },
);

function saveProfile(): void {
    profile.put('/account/profile', {
        preserveScroll: true,
        onSuccess: () => toast.success('Профиль обновлён'),
    });
}

function savePassword(): void {
    password.put('/account/password', {
        preserveScroll: true,
        onSuccess: () => {
            password.reset();
            toast.success('Пароль изменён');
        },
    });
}
</script>

<template>
    <section class="settings">
        <form class="settings__form" @submit.prevent="saveProfile">
            <h2 class="settings__title">Имя и username</h2>
            <AppInput
                v-model="profile.name"
                label="Имя"
                placeholder="Как к вам обращаться"
                required
                :error="profile.errors.name"
            />
            <AppInput
                v-model="profile.username"
                label="Username"
                placeholder="например, my_name"
                required
                :error="profile.errors.username"
            />
            <p class="settings__hint">
                Username должен быть уникальным; можно использовать латинские буквы,
                цифры и _.
            </p>
            <Button type="submit" :disabled="profile.processing">
                Сохранить профиль
            </Button>
        </form>

        <form class="settings__form" @submit.prevent="savePassword">
            <h2 class="settings__title">Смена пароля</h2>
            <AppInput
                v-model="password.current_password"
                type="password"
                label="Текущий пароль"
                placeholder="Введите текущий пароль"
                required
                :error="password.errors.current_password"
            />
            <AppInput
                v-model="password.password"
                type="password"
                label="Новый пароль"
                placeholder="Введите новый пароль"
                required
                :error="password.errors.password"
            />
            <AppInput
                v-model="password.password_confirmation"
                type="password"
                label="Повторите новый пароль"
                placeholder="Повторите пароль"
                required
                :error="password.errors.password_confirmation"
            />
            <Button type="submit" :disabled="password.processing">
                Изменить пароль
            </Button>
        </form>
    </section>
</template>

<style scoped>
.settings {
    display: flex;
    width: 100%;
    flex-direction: column;
    gap: 1.25rem;
}

.settings__form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    padding: 1.25rem;
    border: 1px solid #d7e5e0;
    border-radius: 1rem;
    background: #fff;
}

.settings__title {
    margin: 0;
    color: #203f39;
    font-size: 1.125rem;
}

.settings__hint {
    margin: -0.5rem 0 0;
    color: #78928a;
    font-size: 0.8rem;
    line-height: 1.4;
}
</style>
