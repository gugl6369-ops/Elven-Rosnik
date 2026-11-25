<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

import type { RegisterFormValues } from '@/entities/auth';
import {
    mapZodFieldErrors,
    registerSchema,
    safeParseForm,
} from '@/features/auth/register';
import { AppInput, AppLink, Button } from '@/shared/ui';

type RegisterField = keyof RegisterFormValues;

const RELATED_FIELDS: Record<string, RegisterField[]> = {
    password: ['password', 'password_confirmation'],
};

const form = useForm({
    email: '',
    password: '',
    password_confirmation: '',
});

const clientErrors = ref<Record<string, string>>({});
const touched = ref<Record<string, boolean>>({});
const submitAttempted = ref(false);

const values = (): RegisterFormValues => ({
    email: form.email,
    password: form.password,
    password_confirmation: form.password_confirmation,
});

const fieldsToRefresh = (field: RegisterField): RegisterField[] => {
    const related = RELATED_FIELDS[field] ?? [field];

    return related.filter(
        (name) =>
            touched.value[name] ||
            submitAttempted.value ||
            name === field,
    );
};

const refreshFields = (fields: RegisterField[]): void => {
    const result = registerSchema.safeParse(values());
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

const onBlur = (field: RegisterField): void => {
    touched.value[field] = true;
    refreshFields(fieldsToRefresh(field));
};

const onInput = (field: RegisterField): void => {
    if (!touched.value[field] && !submitAttempted.value) {
        return;
    }

    refreshFields(fieldsToRefresh(field));
};

const validateAll = (): boolean => {
    submitAttempted.value = true;

    for (const name of [
        'email',
        'password',
        'password_confirmation',
    ] as const) {
        touched.value[name] = true;
    }

    const parsed = safeParseForm(registerSchema, values());

    if (!parsed.success) {
        clientErrors.value = parsed.errors;

        return false;
    }

    clientErrors.value = {};

    return true;
};

const clientFieldError = (
    name: RegisterField,
): string | undefined => {
    if (!touched.value[name] && !submitAttempted.value) {
        return undefined;
    }

    return clientErrors.value[name];
};

const fieldError = (name: RegisterField): string | undefined =>
    form.errors[name] ?? clientFieldError(name);

const submit = (): void => {
    if (!validateAll()) {
        return;
    }

    form
        .transform((data) => ({
            ...data,
            username:
                data.email.split('@')[0]?.trim() || data.email.trim(),
        }))
        .post('/register', {
            onFinish: () =>
                form.reset('password', 'password_confirmation'),
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
                v-model="form.email"
                label="Почта"
                placeholder="Почта"
                :error="fieldError('email')"
                @blur="onBlur('email')"
                @input="onInput('email')"
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
            <AppInput
                v-model="form.password_confirmation"
                label="Повторите пароль"
                type="password"
                placeholder="Повторите пароль"
                :error="fieldError('password_confirmation')"
                @blur="onBlur('password_confirmation')"
                @input="onInput('password_confirmation')"
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
            <Button type="submit" :disabled="form.processing">
                Зарегистрироваться
            </Button>
            <AppLink href="/login" class="text-end text-[#203f39]">
                войти
            </AppLink>
        </div>
    </form>
</template>