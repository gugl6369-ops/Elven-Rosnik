<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

import { store as loginStore } from '@/routes/login';
import { AppInput, AppLink, Button } from '@/shared/ui';

const form = useForm({
    username: '',
    password: '',
    remember: false,
});

const submit = (): void => {
    form.post(loginStore.url(), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <form
        class="flex flex-col gap-7 w-full"
        novalidate
        @submit.prevent="submit"
    >
        <div class="flex flex-col gap-7 w-full">
            <AppInput
                v-model="form.username"
                label="Логин"
                placeholder="Логин"
                :error="form.errors.username"
            />
            <AppInput
                v-model="form.password"
                label="Пароль"
                type="password"
                placeholder="Пароль"
                :error="form.errors.password"
            />
        </div>
        <div class="flex justify-between w-full pt-[78px] pb-[65px]">
            <label class="text-[#203f39]">
                <input v-model="form.remember" type="checkbox" />
                Запомнить меня
            </label>
            <p class="text-[#203f39]">Забыли пароль?</p>
        </div>
        <div class="flex flex-col gap-7 w-full gap-[50px]">
            <Button type="submit" :disabled="form.processing">
                Войти
            </Button>
            <AppLink href="/register" class="text-end text-[#203f39]">
                Зарегистрироваться
            </AppLink>
        </div>
    </form>
</template>