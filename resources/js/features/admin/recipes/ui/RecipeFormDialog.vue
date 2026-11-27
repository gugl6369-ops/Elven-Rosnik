<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

import { useAppToast } from '@/shared/lib/toast';
import { Button } from '@/shared/ui';

type PlantOption = { id: number; name: string };

export type AdminRecipeRow = {
    id: number;
    name: string;
    description: string | null;
    additional: string | null;
    advice: string | null;
    recipe: string;
    healthy: boolean;
    plant_ids: number[];
};

const props = defineProps<{
    plantOptions: PlantOption[];
    recipe?: AdminRecipeRow | null;
}>();

const emit = defineEmits<{
    close: [];
}>();

const toast = useAppToast();
const isEdit = computed(() => props.recipe != null);

const form = useForm({
    name: props.recipe?.name ?? '',
    description: props.recipe?.description ?? '',
    additional: props.recipe?.additional ?? '',
    advice: props.recipe?.advice ?? '',
    recipe: props.recipe?.recipe ?? '',
    healthy: props.recipe?.healthy ?? true,
    plant_ids: [...(props.recipe?.plant_ids ?? [])] as number[],
});

watch(
    () => props.recipe,
    (recipe) => {
        if (recipe == null) {
            return;
        }
        form.name = recipe.name;
        form.description = recipe.description ?? '';
        form.additional = recipe.additional ?? '';
        form.advice = recipe.advice ?? '';
        form.recipe = recipe.recipe;
        form.healthy = recipe.healthy;
        form.plant_ids = [...recipe.plant_ids];
    },
    { immediate: true },
);

function togglePlant(id: number): void {
    const set = new Set(form.plant_ids);
    if (set.has(id)) {
        set.delete(id);
    } else {
        set.add(id);
    }
    form.plant_ids = [...set];
}

function submit(): void {
    const url = isEdit.value ? `/admin/recipes/${props.recipe!.id}` : '/admin/recipes';
    const options = {
        onSuccess: () => {
            toast.success(isEdit.value ? 'Рецепт обновлён' : 'Рецепт создан');
            emit('close');
        },
        onError: () => toast.error('Проверьте поля формы'),
    };

    if (isEdit.value) {
        form.put(url, options);
    } else {
        form.post(url, options);
    }
}
</script>

<template>
    <div class="dialog-backdrop" @click.self="emit('close')">
        <div class="dialog">
            <header class="dialog__header">
                <h2>{{ isEdit ? 'Редактировать рецепт' : 'Новый рецепт' }}</h2>
                <button type="button" class="dialog__close" @click="emit('close')">×</button>
            </header>

            <form class="dialog__body" @submit.prevent="submit">
                <label class="field">
                    <span>Название *</span>
                    <input v-model="form.name" required class="field__input" />
                </label>

                <label class="field">
                    <span>Описание</span>
                    <textarea v-model="form.description" rows="2" class="field__input" />
                </label>

                <label class="field">
                    <span>Текст рецепта *</span>
                    <textarea v-model="form.recipe" rows="5" required class="field__input" />
                </label>

                <label class="field field--row">
                    <input v-model="form.healthy" type="checkbox" />
                    <span>Здоровое питание</span>
                </label>

                <div v-if="plantOptions.length" class="plants">
                    <span class="field__label">Растения (необязательно)</span>
                    <div class="plants__list">
                        <label v-for="p in plantOptions" :key="p.id" class="chip">
                            <input
                                type="checkbox"
                                :checked="form.plant_ids.includes(p.id)"
                                @change="togglePlant(p.id)"
                            />
                            {{ p.name }}
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
    background: #fff;
    border: 1px solid #d7e5e0;
}

.dialog__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #e8f0ed;
}

.dialog__header h2 {
    margin: 0;
    font-size: 1.125rem;
}

.dialog__close {
    border: none;
    background: none;
    font-size: 1.5rem;
    cursor: pointer;
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

.field--row {
    flex-direction: row;
    align-items: center;
    gap: 0.5rem;
}

.field__input {
    width: 100%;
    border-radius: 0.5rem;
    border: 1px solid #d7e5e0;
    padding: 0.45rem 0.6rem;
    font: inherit;
}

.plants__list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.35rem;
}

.chip {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    border: 1px solid #d7e5e0;
    border-radius: 999px;
    padding: 0.2rem 0.55rem;
    font-size: 0.8125rem;
}
</style>