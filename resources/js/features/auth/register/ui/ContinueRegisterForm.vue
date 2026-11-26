<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';

import { useAppToast } from '@/entities/toast';
import type {
    AuthPageProps,
    ContinueRegisterFormValues,
} from '@/entities/user';
import { useContinueRegisterDraftStore } from '@/entities/user';
import {
    continueRegisterSchema,
    mapZodFieldErrors,
    safeParseForm,
} from '@/features/auth/register';
import { AppInput, Button } from '@/shared/ui';

const draftStore = useContinueRegisterDraftStore();
const toast = useAppToast();

type ContinueField = keyof ContinueRegisterFormValues;

const page = usePage<AuthPageProps>();

const defaultAvatarUrl = '/image/avatar-loading.svg';

const avatarPreview = ref<string>(
    page.props.auth.user?.avatar ?? defaultAvatarUrl,
);

const form = useForm({
    username: page.props.auth.user?.username ?? '',
    name: page.props.auth.user?.name ?? '',
    status: page.props.auth.user?.status ?? '',
    avatar: null as File | null,
});

const clientErrors = ref<Record<string, string>>({});
const touched = ref<Record<string, boolean>>({});
const submitAttempted = ref(false);

const values = (): ContinueRegisterFormValues => ({
    username: form.username,
    name: form.name,
    status: form.status,
});

const refreshFields = (fields: ContinueField[]): void => {
    const result = continueRegisterSchema.safeParse(values());
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

const onBlur = (field: ContinueField): void => {
    touched.value[field] = true;
    refreshFields([field]);
};

const onInput = (field: ContinueField): void => {
    if (!touched.value[field] && !submitAttempted.value) {
        return;
    }

    refreshFields([field]);
};

const validateAll = (): boolean => {
    submitAttempted.value = true;

    for (const name of ['username', 'name', 'status'] as const) {
        touched.value[name] = true;
    }

    const parsed = safeParseForm(continueRegisterSchema, values());

    if (!parsed.success) {
        clientErrors.value = parsed.errors;

        return false;
    }

    clientErrors.value = {};

    return true;
};

const clientFieldError = (
    name: ContinueField,
): string | undefined => {
    if (!touched.value[name] && !submitAttempted.value) {
        return undefined;
    }

    return clientErrors.value[name];
};

const fieldError = (name: ContinueField): string | undefined =>
    form.errors[name] ?? clientFieldError(name);

const usernameError = computed(() => fieldError('username'));

const onAvatarChange = (event: Event): void => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];

    if (!file) {
        return;
    }

    form.avatar = file;
    avatarPreview.value = URL.createObjectURL(file);
};

const firstClientError = (): string | undefined => {
    const errors = Object.values(clientErrors.value);

    return errors.find((message) => message.trim() !== '');
};

const submit = (): void => {
    if (!validateAll()) {
        toast.error(
            firstClientError() ?? 'Проверьте поля формы',
        );

        return;
    }

    form.post('/register/continue', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            draftStore.clear();
            toast.success('Профиль сохранён');
        },
        onError: (errors) => {
            const first = Object.values(errors).find(
                (message) => typeof message === 'string' && message.trim(),
            );

            toast.error(
                typeof first === 'string'
                    ? first
                    : 'Не удалось сохранить профиль',
            );
        },
    });
};

onMounted(() => {
    if (draftStore.hasDraft()) {
        form.username = draftStore.username;
        form.name = draftStore.name;
        form.status = draftStore.status;
    } else {
        draftStore.syncFrom({
            username: form.username,
            name: form.name,
            status: form.status,
        });
    }
});

watch(
    () => ({
        username: form.username,
        name: form.name,
        status: form.status,
    }),
    (fields) => {
        draftStore.syncFrom(fields);
    },
);

watch(
    () => page.props.auth.user,
    (user) => {
        if (!user || draftStore.hasDraft()) {
            return;
        }

        form.username = user.username;
        form.name = user.name ?? '';
        form.status = user.status ?? '';

        if (!form.avatar) {
            avatarPreview.value = user.avatar ?? defaultAvatarUrl;
        }
    },
);
</script>

<template>
    <form
        class="w-full h-full flex flex-col items-center justify-between gap-10 py-[15%]"
        novalidate
        @submit.prevent="submit"
    >
        <h2 class="text-[46px] text-center">Создание профиля</h2>
        <label
            class="w-37.5 h-37.5 border-2 border-[#203f39] rounded-full bg-cover bg-center bg-no-repeat cursor-pointer"
            :style="{ backgroundImage: `url('${avatarPreview}')` }"
        >
            <input
                class="sr-only"
                type="file"
                accept="image/*"
                @change="onAvatarChange"
            />
        </label>
        <div class="flex flex-col gap-7 w-full">
            <label class="flex flex-col gap-2 w-full">
                <span class="text-sm text-[#444444]">Уникальное имя</span>
                <span class="flex items-center gap-1 w-full">
                    @
                    <input
                        v-model="form.username"
                        class="flex-1 border-b border-[#808080] bg-transparent text-sm text-[#203f39] outline-none py-1"
                        type="text"
                        placeholder="Уникальное имя"
                        @blur="onBlur('username')"
                        @input="onInput('username')"
                    />
                </span>
                <span
                    v-if="usernameError"
                    class="text-sm text-red-600"
                >
                    {{ usernameError }}
                </span>
            </label>
            <AppInput
                v-model="form.name"
                label="Имя"
                placeholder="Имя"
                required
                :error="fieldError('name')"
                @blur="onBlur('name')"
                @input="onInput('name')"
            />
            <AppInput
                v-model="form.status"
                label="Статус"
                placeholder="Статус"
                required
                :error="fieldError('status')"
                @blur="onBlur('status')"
                @input="onInput('status')"
            />
        </div>
        <Button type="submit" :disabled="form.processing">
            Сохранить
        </Button>
    </form>
</template>