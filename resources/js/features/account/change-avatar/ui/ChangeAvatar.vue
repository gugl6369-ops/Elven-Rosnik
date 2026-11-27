<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

import type { AuthUser } from '@/entities/user';
import { useAppToast } from '@/shared/lib/toast';
import { Button } from '@/shared/ui';

const props = defineProps<{
    user: AuthUser;
}>();

const toast = useAppToast();

const defaultAvatarUrl = '/image/avatar-loading.svg';

const avatarPreview = ref<string>(defaultAvatarUrl);
/** Kept outside useForm so Inertia can append a real File to multipart FormData. */
const avatarFile = ref<File | null>(null);

const form = useForm({});

const avatarError = computed(() => {
    const message = form.errors.avatar;

    return typeof message === 'string' && message.trim() !== ''
        ? message
        : undefined;
});

const syncPreviewFromUser = (): void => {
    avatarPreview.value = props.user.avatar?.trim() || defaultAvatarUrl;
    avatarFile.value = null;
};

watch(
    () => props.user.avatar,
    () => {
        if (!avatarFile.value) {
            syncPreviewFromUser();
        }
    },
    { immediate: true },
);

const onAvatarChange = (event: Event): void => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];

    if (!file) {
        return;
    }

    avatarFile.value = file;
    avatarPreview.value = URL.createObjectURL(file);
};

const submit = (): void => {
    const file = avatarFile.value;

    if (!file) {
        toast.error('Выберите изображение');

        return;
    }

    form
        .transform(() => ({
            avatar: file,
        }))
        .post('/account/avatar', {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                avatarFile.value = null;
                toast.success('Аватар обновлён');
            },
            onError: (errors) => {
                const avatarMessage =
                    typeof errors.avatar === 'string' ? errors.avatar : null;

                if (avatarMessage?.trim()) {
                    toast.error(avatarMessage);

                    return;
                }

                toast.error('Не удалось обновить аватар');
            },
        });
};
</script>

<template>
    <div class="flex flex-col items-center gap-3">
        <span class="text-sm text-[#444444]">Аватар</span>
        <label
            class="h-28 w-28 cursor-pointer rounded-full border-2 border-[#21483f] bg-cover bg-center bg-no-repeat object-cover"
            :class="{ 'border-red-600': avatarError }"
            :style="{ backgroundImage: `url('${avatarPreview}')` }"
        >
            <input
                class="sr-only"
                name="avatar"
                type="file"
                accept="image/jpeg,image/png,image/webp"
                @change="onAvatarChange"
            />
        </label>
        <span
            v-if="avatarError"
            class="text-center text-sm text-red-600"
        >
            {{ avatarError }}
        </span>
        <Button
            type="button"
            :disabled="form.processing || !avatarFile"
            @click="submit"
        >
            Сохранить аватар
        </Button>
    </div>
</template>