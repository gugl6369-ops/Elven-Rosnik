<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

import { useAppToast } from '@/shared/lib/toast';
import { Button } from '@/shared/ui';

const emit = defineEmits<{
    close: [];
}>();

const toast = useAppToast();
const mediaFile = ref<File | null>(null);

const defaultUntil = (): string => {
    const date = new Date();
    date.setDate(date.getDate() + 2);
    return date.toISOString().slice(0, 16);
};

const form = useForm({
    caption: '',
    active_until: defaultUntil(),
    is_active: true,
});

const onMediaChange = (event: Event): void => {
    const input = event.target as HTMLInputElement;
    mediaFile.value = input.files?.[0] ?? null;
};

const submit = (): void => {
    if (mediaFile.value === null) {
        toast.error('Выберите изображение');
        return;
    }

    form
        .transform((data) => ({
            ...data,
            is_active: data.is_active ? '1' : '0',
            media: mediaFile.value as File,
        }))
        .post('/admin/stories', {
            forceFormData: true,
            onSuccess: () => {
                emit('close');
            },
            onError: () => toast.error('Не удалось создать историю'),
        });
};
</script>

<template>
    <div class="dialog-backdrop" role="presentation" @click.self="emit('close')">
        <div class="dialog" role="dialog" aria-labelledby="story-create-title">
            <header class="dialog__header">
                <h2 id="story-create-title" class="dialog__title">Новая история</h2>
                <button type="button" class="dialog__close" @click="emit('close')">×</button>
            </header>

            <form class="dialog__body" @submit.prevent="submit">
                <label class="field">
                    <span class="field__label">Изображение</span>
                    <input type="file" accept="image/*" required @change="onMediaChange" />
                </label>

                <label class="field">
                    <span class="field__label">Подпись</span>
                    <textarea v-model="form.caption" rows="3" class="field__input" />
                </label>

                <label class="field">
                    <span class="field__label">Активна до</span>
                    <input v-model="form.active_until" type="datetime-local" required class="field__input" />
                </label>

                <label class="field field--row">
                    <input v-model="form.is_active" type="checkbox" />
                    <span>Показывать в ленте</span>
                </label>

                <Button type="submit" :disabled="form.processing">Создать</Button>
            </form>
        </div>
    </div>
</template>

<style scoped>
.dialog-backdrop {
    position: fixed;
    inset: 0;
    z-index: 50;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgb(32 63 57 / 45%);
    padding: 1rem;
}

.dialog {
    width: min(100%, 28rem);
    border-radius: 1rem;
    border: 1px solid #d7e5e0;
    background: #fff;
    box-shadow: 0 16px 40px rgb(32 63 57 / 12%);
}

.dialog__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #e8f0ed;
    padding: 1rem 1.25rem;
}

.dialog__title {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
}

.dialog__close {
    border: none;
    background: none;
    font-size: 1.5rem;
    line-height: 1;
    color: #78928a;
    cursor: pointer;
}

.dialog__body {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    padding: 1.25rem;
}

.field {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    font-size: 0.875rem;
}

.field--row {
    flex-direction: row;
    align-items: center;
    gap: 0.5rem;
}

.field__label {
    font-weight: 500;
    color: #21483f;
}

.field__input {
    width: 100%;
    border-radius: 0.5rem;
    border: 1px solid #d7e5e0;
    padding: 0.5rem 0.65rem;
    font: inherit;
    color: #203f39;
}
</style>
