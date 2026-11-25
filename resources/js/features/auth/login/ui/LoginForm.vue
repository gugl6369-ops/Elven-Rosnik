<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

import type { LoginFormValues } from '@/entities/auth';
import {
    loginSchema,
    mapZodFieldErrors,
    safeParseForm,
} from '@/features/auth/login';
import { store as loginStore } from '@/routes/login';
import { AppInput, AppLink, Button } from '@/shared/ui';

type LoginField = keyof LoginFormValues;

const form = useForm({
    username: '',
    password: '',
    remember: false,
});

const clientErrors = ref<Record<string, string>>({});
const touched = ref<Record<string, boolean>>({});
const submitAttempted = ref(false);

const values = (): LoginFormValues => ({
    username: form.username,
    password: form.password,
});

const refreshFields = (fields: LoginField[]): void => {
    const result = loginSchema.safeParse(values());
    const errors = result.success
        ? {}
        : mapZodFieldErrors(result.error);

    for (const name of fields) {
        const show = touched.value[name] || submitAttempted.value;

        if (!show) {
            continue;
        }

        if (errors[name]) {
            clientErrors.value[name] = errors[name];
        } else {
            delete clientErrors.value[name];
        }
    }
};

const onBlur = (field: LoginField): void => {
    touched.value[field] = true;
    refreshFields([field]);
};

const onInput = (field: LoginField): void => {
    if (!touched.value[field] && !submitAttempted.value) {
        return;
    }

    refreshFields([field]);
};

const validateAll = (): boolean => {
    submitAttempted.value = true;

    for (const name of ['username', 'password'] as const) {
        touched.value[name] = true;
    }

    const parsed = safeParseForm(loginSchema, values());

    if (!parsed.success) {
        clientErrors.value = parsed.errors;

        return false;
    }

    clientErrors.value = {};

    return true;
};

const clientFieldError = (
    name: LoginField,
): string | undefined => {
    if (!touched.value[name] && !submitAttempted.value) {
        return undefined;
    }

    return clientErrors.value[name];
};

const fieldError = (name: LoginField): string | undefined =>
    form.errors[name] ?? clientFieldError(name);

const submit = (): void => {
    if (!validateAll()) {
        return;
    }

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
                :error="fieldError('username')"
                @blur="onBlur('username')"
                @input="onInput('username')"
            />
            <AppInput
                v-model="form.password"
                label="Пароль"
                type="password"
                placeholder="Пароль"
                :error="fieldError('password')"
                @blur="onBlur('password')"
                @input="onInput('password')"
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