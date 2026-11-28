<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';

import { useAppToast } from '@/shared/lib/toast';
import {
    useContinueRegisterDraftStore,
    useRegisterDraftStore,
} from '@/entities/stores';
import { AppInput, Button } from '@/shared/ui';

const registerDraft = useRegisterDraftStore();
const draftStore = useContinueRegisterDraftStore();
const toast = useAppToast();

const defaultAvatarUrl = '/image/avatar-loading.svg';

const avatarPreview = ref<string>(defaultAvatarUrl);
const avatarFile = ref<File | null>(null);

const form = useForm({
    email: registerDraft.email,
    password: registerDraft.password,
    password_confirmation: registerDraft.password_confirmation,
    username: '',
    name: '',
    status: '',
});

const avatarError = computed(() => {
    const message = form.errors.avatar;

    return typeof message === 'string' && message.trim() !== ''
        ? message
        : undefined;
});

const onAvatarChange = (event: Event): void => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];

    if (!file) {
        return;
    }

    avatarFile.value = file;
    avatarPreview.value = URL.createObjectURL(file);
};

const ensureStep1Draft = (): boolean => {
    registerDraft.hydrateSecretsFromSession();

    if (registerDraft.hasStep1()) {
        form.email = registerDraft.email;
        form.password = registerDraft.password;
        form.password_confirmation = registerDraft.password_confirmation;

        return true;
    }

    toast.error('Сначала укажите почту и пароль');
    router.visit('/register');

    return false;
};

const submit = (): void => {
    if (!ensureStep1Draft()) {
        return;
    }

    const file = avatarFile.value;

    form
        .transform((data) => {
            const payload: Record<string, string | File> = {
                email: registerDraft.email,
                password: registerDraft.password,
                password_confirmation: registerDraft.password_confirmation,
                username: data.username,
                name: data.name,
                status: data.status,
            };

            if (file) {
                payload.avatar = file;
            }

            return payload;
        })
        .post('/register/continue', {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                registerDraft.clear();
                draftStore.clear();
                toast.success('Регистрация завершена');
            },
            onError: (errors) => {
                const avatarMessage =
                    typeof errors.avatar === 'string' ? errors.avatar : null;

                const first = Object.values(errors).find(
                    (message) => typeof message === 'string' && message.trim(),
                );

                if (avatarMessage?.trim()) {
                    toast.error(avatarMessage);

                    return;
                }

                toast.error(
                    typeof first === 'string'
                        ? first
                        : 'Не удалось завершить регистрацию',
                );
            },
        });
};

onMounted(() => {
    if (!ensureStep1Draft()) {
        return;
    }

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
</script>

<template>
    <form
        class="w-full h-full flex flex-col items-center justify-between gap-10 py-[15%]"
        novalidate
        @submit.prevent="submit"
    >
        <h2 class="text-[46px] text-center">Создание профиля</h2>
        <div class="flex flex-col items-center gap-2 w-full">
            <label
                class="w-37.5 h-37.5 border-2 border-[#203f39] rounded-full bg-cover bg-center bg-no-repeat cursor-pointer"
                :class="{ 'border-red-600': avatarError }"
                :style="{ backgroundImage: `url('${avatarPreview}')` }"
            >
                <input
                    class="sr-only"
                    name="avatar"
                    type="file"
                    accept="image/*"
                    @change="onAvatarChange"
                />
            </label>
            <span
                v-if="avatarError"
                class="text-sm text-red-600 text-center"
            >
                {{ avatarError }}
            </span>
        </div>
        <div class="flex flex-col gap-7 w-full">
            <label class="flex flex-col gap-2 w-full">
                <span class="text-sm text-[#444444]">Уникальное имя</span>
                <span class="flex items-center gap-1 w-full">
                    @
                    <input
                        v-model="form.username"
                        class="flex-1 border-b border-[#808080] bg-transparent text-sm text-[#203f39] outline-none py-1"
                        :class="{ 'border-red-600': form.errors.username }"
                        type="text"
                        placeholder="Уникальное имя"
                    />
                </span>
                <span
                    v-if="form.errors.username"
                    class="text-sm text-red-600"
                >
                    {{ form.errors.username }}
                </span>
            </label>
            <AppInput
                v-model="form.name"
                label="Имя"
                placeholder="Имя"
                :error="form.errors.name"
            />
            <AppInput
                v-model="form.status"
                label="Статус"
                placeholder="Статус"
                :error="form.errors.status"
            />
        </div>
        <Button type="submit" :disabled="form.processing">
            Сохранить
        </Button>
    </form>
</template>
