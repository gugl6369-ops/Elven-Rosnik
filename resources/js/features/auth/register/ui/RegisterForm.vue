<script setup lang="ts">
import { onMounted } from 'vue';
import { router, useForm } from '@inertiajs/vue3';

import { useRegisterDraftStore } from '@/entities/stores';
import { AppInput, AppLink, Button } from '@/shared/ui';

const registerDraft = useRegisterDraftStore();

const form = useForm({
    email: registerDraft.email,
    password: registerDraft.password,
    password_confirmation: registerDraft.password_confirmation,
});

const submit = (): void => {
    registerDraft.syncStep1({
        email: form.email,
        password: form.password,
        password_confirmation: form.password_confirmation,
    });

    form.reset();

    router.visit('/register/continue');
};

onMounted(() => {
    registerDraft.hydrateSecretsFromSession();
    form.email = registerDraft.email;
    form.password = registerDraft.password;
    form.password_confirmation = registerDraft.password_confirmation;
});
</script>

<template>
    <form
        class="flex flex-col gap-7 w-full"
        novalidate
        @submit.prevent="submit"
    >
        <div class="flex flex-col gap-7 w-full">
            <AppInput
                v-model="form.email"
                label="Почта"
                placeholder="Почта"
                :error="form.errors.email"
            />
            <AppInput
                v-model="form.password"
                label="Пароль"
                type="password"
                placeholder="Пароль"
                :error="form.errors.password"
            />
            <AppInput
                v-model="form.password_confirmation"
                label="Повторите пароль"
                type="password"
                placeholder="Повторите пароль"
                :error="form.errors.password_confirmation"
            />
        </div>
        <div class="flex gap-3 items-start w-full py-[50px]">
            <input type="checkbox" />
            <label class="text-[#203f39] max-w-[80%]">
                Я соглашаюсь с Политикой конфиденциальности и Условиями
                пользования
            </label>
        </div>
        <div class="flex flex-col gap-7 w-full gap-[50px]">
            <Button type="submit">
                Зарегистрироваться
            </Button>
            <AppLink href="/login" class="text-end text-[#203f39]">
                войти
            </AppLink>
        </div>
    </form>
</template>