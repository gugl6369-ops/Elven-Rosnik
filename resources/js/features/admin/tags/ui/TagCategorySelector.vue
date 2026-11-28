<script setup lang="ts">
import { computed, reactive } from 'vue';
import type { AdminTagOption, PendingTag, TagCategory } from '../model/types';

const props = defineProps<{
    tags: AdminTagOption[];
    modelValue: number[];
    pendingTags: PendingTag[];
}>();

const emit = defineEmits<{
    'update:modelValue': [value: number[]];
    'update:pendingTags': [value: PendingTag[]];
}>();

const categories: { value: TagCategory; title: string }[] = [
    { value: 'эффекты', title: 'Эффекты' },
    { value: 'противопоказания', title: 'Противопоказания' },
    { value: 'возможные применения', title: 'Возможные применения' },
];

const searches = reactive<Record<TagCategory, string>>({
    эффекты: '',
    противопоказания: '',
    'возможные применения': '',
});
const categoryValues = new Set(categories.map((category) => category.value));
const legacyTags = computed(() => props.tags.filter((tag) => !categoryValues.has(tag.category as TagCategory)));

function filteredTags(category: TagCategory): AdminTagOption[] {
    const query = searches[category].trim().toLocaleLowerCase('ru');
    return props.tags
        .filter((tag) => tag.category === category)
        .filter((tag) => query === '' || tag.name.toLocaleLowerCase('ru').includes(query))
        .slice(0, 30);
}

function exactTagExists(category: TagCategory, name: string): boolean {
    const normalized = name.trim().toLocaleLowerCase('ru');
    return props.tags.some(
        (tag) => tag.category === category && tag.name.toLocaleLowerCase('ru') === normalized,
    );
}

function hasPendingTag(category: TagCategory, name: string): boolean {
    const normalized = name.trim().toLocaleLowerCase('ru');
    return props.pendingTags.some(
        (tag) => tag.category === category && tag.name.toLocaleLowerCase('ru') === normalized,
    );
}

function toggleTag(tagId: number): void {
    const selected = new Set(props.modelValue);
    if (selected.has(tagId)) selected.delete(tagId);
    else selected.add(tagId);
    emit('update:modelValue', [...selected]);
}

function addTag(category: TagCategory): void {
    const name = searches[category].trim();
    if (!name || exactTagExists(category, name) || hasPendingTag(category, name)) return;

    emit('update:pendingTags', [...props.pendingTags, { name, category }]);
    searches[category] = '';
}

function removePendingTag(tag: PendingTag): void {
    emit(
        'update:pendingTags',
        props.pendingTags.filter((item) => item !== tag),
    );
}

function selectedLegacy(tagId: number): boolean {
    return props.modelValue.includes(tagId);
}
</script>

<template>
    <div class="tag-selector">
        <section v-for="category in categories" :key="category.value" class="tag-selector__category">
            <h3 class="tag-selector__title">{{ category.title }}</h3>
            <input
                v-model="searches[category.value]"
                type="search"
                class="tag-selector__search"
                :placeholder="`Найти тег в категории «${category.value}»`"
                :aria-label="`Поиск тегов: ${category.value}`"
            />

            <div class="tag-selector__options">
                <button
                    v-for="tag in filteredTags(category.value)"
                    :key="tag.id"
                    type="button"
                    class="tag-selector__chip"
                    :class="{ 'tag-selector__chip--selected': modelValue.includes(tag.id) }"
                    :aria-pressed="modelValue.includes(tag.id)"
                    @click="toggleTag(tag.id)"
                >
                    {{ tag.name }}
                    <span aria-hidden="true">{{ modelValue.includes(tag.id) ? '✓' : '+' }}</span>
                </button>
                <span v-if="filteredTags(category.value).length === 0" class="tag-selector__empty">
                    Подходящих тегов нет.
                </span>
            </div>

            <button
                v-if="searches[category.value].trim() && !exactTagExists(category.value, searches[category.value]) && !hasPendingTag(category.value, searches[category.value])"
                type="button"
                class="tag-selector__add"
                @click="addTag(category.value)"
            >
                + Добавить «{{ searches[category.value].trim() }}» в общую базу
            </button>

            <div v-if="pendingTags.some((tag) => tag.category === category.value)" class="tag-selector__pending">
                <span
                    v-for="tag in pendingTags.filter((item) => item.category === category.value)"
                    :key="`${tag.category}:${tag.name}`"
                    class="tag-selector__pending-chip"
                >
                    {{ tag.name }} <small>новый</small>
                    <button type="button" :aria-label="`Удалить тег ${tag.name}`" @click="removePendingTag(tag)">×</button>
                </span>
            </div>
        </section>

        <details v-if="legacyTags.length" class="tag-selector__legacy">
            <summary>Теги старых категорий ({{ legacyTags.length }})</summary>
            <p>Оставлены для уже существующих карточек и рецептов.</p>
            <div class="tag-selector__options">
                <button
                    v-for="tag in legacyTags"
                    :key="tag.id"
                    type="button"
                    class="tag-selector__chip"
                    :class="{ 'tag-selector__chip--selected': selectedLegacy(tag.id) }"
                    :aria-pressed="selectedLegacy(tag.id)"
                    @click="toggleTag(tag.id)"
                >
                    {{ tag.name }} <small>{{ tag.category }}</small>
                    <span aria-hidden="true">{{ selectedLegacy(tag.id) ? '✓' : '+' }}</span>
                </button>
            </div>
        </details>
    </div>
</template>

<style scoped>
.tag-selector {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.tag-selector__category,
.tag-selector__legacy {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    padding: 0.85rem;
    border: 1px solid #d7e5e0;
    border-radius: 0.75rem;
}

.tag-selector__title {
    margin: 0;
    color: #21483f;
    font-size: 0.9rem;
}

.tag-selector__search {
    width: 100%;
    padding: 0.45rem 0.6rem;
    border: 1px solid #cbdcd5;
    border-radius: 0.5rem;
    color: #203f39;
    font: inherit;
    font-size: 0.85rem;
}

.tag-selector__options,
.tag-selector__pending {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
}

.tag-selector__chip,
.tag-selector__pending-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.3rem 0.55rem;
    border: 1px solid #d7e5e0;
    border-radius: 999px;
    background: #fff;
    color: #45645a;
    font: inherit;
    font-size: 0.78rem;
}

.tag-selector__chip { cursor: pointer; }

.tag-selector__chip--selected {
    border-color: #7da99a;
    background: #eaf4ef;
    color: #21483f;
}

.tag-selector__chip small,
.tag-selector__pending-chip small {
    color: #78928a;
    font-size: 0.68rem;
}

.tag-selector__empty,
.tag-selector__legacy p {
    color: #78928a;
    font-size: 0.78rem;
}

.tag-selector__add {
    align-self: flex-start;
    padding: 0.25rem 0;
    border: 0;
    background: transparent;
    color: #2f6d61;
    font: inherit;
    font-size: 0.8rem;
    text-align: left;
    cursor: pointer;
}

.tag-selector__pending-chip {
    border-color: #b5c9bf;
    background: #f3f8f5;
}

.tag-selector__pending-chip button {
    border: 0;
    background: transparent;
    color: #78928a;
    font-size: 1rem;
    cursor: pointer;
}

.tag-selector__legacy summary {
    color: #5a746c;
    font-size: 0.82rem;
    cursor: pointer;
}

.tag-selector__legacy p { margin: 0; }
</style>
