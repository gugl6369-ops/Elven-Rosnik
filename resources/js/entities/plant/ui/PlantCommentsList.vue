<script setup lang="ts">
import type { PlantCommentPreview } from '@/entities/plant';

withDefaults(
    defineProps<{
        comments?: PlantCommentPreview[];
        commentsCount?: number;
    }>(),
    {
        comments: () => [],
        commentsCount: 0,
    },
);

function formatDate(iso: string): string {
    try {
        return new Intl.DateTimeFormat('ru-RU', {
            dateStyle: 'medium',
            timeStyle: 'short',
        }).format(new Date(iso));
    } catch {
        return iso;
    }
}

function authorLabel(comment: PlantCommentPreview): string {
    return comment.author.name?.trim() || comment.author.username;
}
</script>

<template>
    <section class="plant-comments" aria-labelledby="plant-comments-heading">
        <header class="plant-comments__header">
            <h2 id="plant-comments-heading" class="plant-comments__title">
                Комментарии
            </h2>
            <span class="plant-comments__count" aria-live="polite">
                {{ commentsCount }}
            </span>
        </header>

        <p
            v-if="comments.length === 0"
            class="plant-comments__empty"
        >
            <template v-if="commentsCount > 0">
                Есть {{ commentsCount }} комментариев — список появится, когда
                бэкенд отдаст `plant.comments`.
            </template>
            <template v-else>
                Пока нет комментариев.
            </template>
        </p>

        <ul v-else class="plant-comments__list">
            <li
                v-for="comment in comments"
                :key="comment.id"
                class="plant-comments__item"
            >
                <div class="plant-comments__meta">
                    <span class="plant-comments__author">
                        {{ authorLabel(comment) }}
                    </span>
                    <time
                        class="plant-comments__time"
                        :datetime="comment.created_at"
                    >
                        {{ formatDate(comment.created_at) }}
                    </time>
                </div>
                <p class="plant-comments__content">
                    {{ comment.content }}
                </p>
            </li>
        </ul>

        <!-- Presentational placeholder; wire to POST when API exists -->
        <form
            class="plant-comments__form"
            aria-label="Новый комментарий (скоро)"
            @submit.prevent
        >
            <label class="plant-comments__form-label">
                <span class="plant-comments__form-caption">Ваш комментарий</span>
                <textarea
                    class="plant-comments__textarea"
                    rows="3"
                    disabled
                    placeholder="Вход через аккаунт и API появятся позже"
                />
            </label>
            <p class="plant-comments__form-hint">
                Форма только для вёрстки — отправка не подключена.
            </p>
        </form>
    </section>
</template>

<style scoped>
.plant-comments {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    padding: 1.25rem;
    border-radius: 1rem;
    border: 1px solid #d7e5e0;
    background: #fff;
}

.plant-comments__header {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
}

.plant-comments__title {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: #203f39;
}

.plant-comments__count {
    font-size: 0.875rem;
    font-weight: 500;
    color: #78928a;
}

.plant-comments__empty {
    margin: 0;
    font-size: 0.875rem;
    color: #5a746c;
    line-height: 1.45;
}

.plant-comments__list {
    margin: 0;
    padding: 0;
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.plant-comments__item {
    padding-bottom: 1rem;
    border-bottom: 1px solid #e8f0ed;
}

.plant-comments__item:last-child {
    padding-bottom: 0;
    border-bottom: none;
}

.plant-comments__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.35rem 0.75rem;
    margin-bottom: 0.35rem;
    font-size: 0.8125rem;
}

.plant-comments__author {
    font-weight: 600;
    color: #21483f;
}

.plant-comments__time {
    color: #78928a;
}

.plant-comments__content {
    margin: 0;
    font-size: 0.9375rem;
    line-height: 1.5;
    color: #203f39;
    white-space: pre-wrap;
}

.plant-comments__form {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-top: 0.25rem;
    padding-top: 1rem;
    border-top: 1px dashed #d7e5e0;
}

.plant-comments__form-label {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}

.plant-comments__form-caption {
    font-size: 0.8125rem;
    font-weight: 500;
    color: #78928a;
}

.plant-comments__textarea {
    width: 100%;
    resize: vertical;
    padding: 0.65rem 0.75rem;
    border-radius: 0.5rem;
    border: 1px solid #d7e5e0;
    font: inherit;
    color: #203f39;
    background: #f5f8f7;
}

.plant-comments__form-hint {
    margin: 0;
    font-size: 0.75rem;
    color: #78928a;
}
</style>