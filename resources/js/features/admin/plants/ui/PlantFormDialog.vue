<script setup lang="ts">
import type { PlantGenus, PlantListItem } from '@/entities/plant';
import { useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

import { useAppToast } from '@/shared/lib/toast';
import { Button } from '@/shared/ui';

type TagOption = { id: number; name: string; category: string };

type AdminPlantRow = PlantListItem & {
    tag_ids?: number[];
};

const props = defineProps<{
    genera: PlantGenus[];
    tags: TagOption[];
    plant?: AdminPlantRow | null;
}>();

const emit = defineEmits<{
    close: [];
}>();

const toast = useAppToast();
const imageFile = ref<File | null>(null);
const isEdit = computed(() => props.plant != null);

const form = useForm({
    genus_id: props.plant?.genus?.id ?? (props.genera[0]?.id ?? ''),
    name: props.plant?.name ?? '',
    description: props.plant?.description ?? '',
    height: props.plant?.height ?? '',
    watering_frequency: props.plant?.watering_frequency ?? '',
    sunlight: props.plant?.sunlight ?? '',
    temp_min: props.plant?.temp_min ?? '',
    temp_max: props.plant?.temp_max ?? '',
    tag_ids: [...(props.plant?.tag_ids ?? [])] as number[],
});

watch(
    () => props.plant,
    (plant) => {
        if (plant == null) {
            return;
        }
        form.genus_id = plant.genus?.id ?? form.genus_id;
        form.name = plant.name;
        form.description = plant.description ?? '';
        form.height = plant.height ?? '';
        form.watering_frequency = plant.watering_frequency ?? '';
        form.sunlight = plant.sunlight ?? '';
        form.temp_min = plant.temp_min ?? '';
        form.temp_max = plant.temp_max ?? '';
        form.tag_ids = [...(plant.tag_ids ?? [])];
    },
    { immediate: true },
);

function onImageChange(event: Event): void {
    imageFile.value = (event.target as HTMLInputElement).files?.[0] ?? null;
}

function toggleTag(id: number): void {
    const set = new Set(form.tag_ids);
    if (set.has(id)) {
        set.delete(id);
    } else {
        set.add(id);
    }
    form.tag_ids = [...set];
}

function submit(): void {
    const url = isEdit.value ? `/admin/plants/${props.plant!.id}` : '/admin/plants';

    const options = {
        forceFormData: imageFile.value !== null,
        onSuccess: () => {
            toast.success(isEdit.value ? 'Карточка обновлена' : 'Карточка создана');
            emit('close');
        },
        onError: () => toast.error('Проверьте поля формы'),
    };

    form.transform((data) => {
        const payload: Record<string, unknown> = { ...data };
        if (imageFile.value) {
            payload.image = imageFile.value;
        }
        return payload;
    });

    if (isEdit.value) {
        form.put(url, options);
    } else {
        form.post(url, options);
    }
}
</script>

<template>
    <div class="dialog-backdrop" @click.self="emit('close')">
        <div class="dialog" role="dialog">
            <header class="dialog__header">
                <h2 class="dialog__title">{{ isEdit ? 'Редактировать' : 'Новая карточка' }}</h2>
                <button type="button" class="dialog__close" @click="emit('close')">×</button>
            </header>

            <form class="dialog__body" @submit.prevent="submit">
                <label class="field">
                    <span class="field__label">Род *</span>
                    <select v-model="form.genus_id" required class="field__input">
                        <option v-for="g in genera" :key="g.id" :value="g.id">{{ g.genus_name }}</option>
                    </select>
                </label>

                <label class="field">
                    <span class="field__label">Название *</span>
                    <input v-model="form.name" required class="field__input" />
                </label>

                <label class="field">
                    <span class="field__label">Описание</span>
                    <textarea v-model="form.description" rows="3" class="field__input" />
                </label>

                <label class="field">
                    <span class="field__label">Изображение</span>
                    <input type="file" accept="image/*" @change="onImageChange" />
                </label>

                <div class="grid-2">
                    <label class="field">
                        <span class="field__label">Высота</span>
                        <input v-model="form.height" class="field__input" />
                    </label>
                    <label class="field">
                        <span class="field__label">Полив</span>
                        <input v-model="form.watering_frequency" class="field__input" />
                    </label>
                </div>

                <label class="field">
                    <span class="field__label">Освещение</span>
                    <input v-model="form.sunlight" class="field__input" />
                </label>

                <div v-if="tags.length" class="tags">
                    <span class="field__label">Теги</span>
                    <div class="tags__list">
                        <label v-for="tag in tags" :key="tag.id" class="tag-chip">
                            <input
                                type="checkbox"
                                :checked="form.tag_ids.includes(tag.id)"
                                @change="toggleTag(tag.id)"
                            />
                            {{ tag.name }}
                        </label>
                    </div>
                </div>

                <Button type="submit" :disabled="form.processing">Сохранить</Button>
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
    width: min(100%, 32rem);
    max-height: 90vh;
    overflow: auto;
    border-radius: 1rem;
    border: 1px solid #d7e5e0;
    background: #fff;
}

.dialog__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #e8f0ed;
}

.dialog__title {
    margin: 0;
    font-size: 1.125rem;
}

.dialog__close {
    border: none;
    background: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #78928a;
}

.dialog__body {
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
    padding: 1.25rem;
}

.field {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
    font-size: 0.875rem;
}

.field__label {
    font-weight: 500;
    color: #21483f;
}

.field__input {
    width: 100%;
    border-radius: 0.5rem;
    border: 1px solid #d7e5e0;
    padding: 0.45rem 0.6rem;
    font: inherit;
}

.grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}

.tags__list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.35rem;
}

.tag-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    border-radius: 999px;
    border: 1px solid #d7e5e0;
    padding: 0.2rem 0.55rem;
    font-size: 0.8125rem;
}
</style>