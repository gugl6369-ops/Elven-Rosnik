<script setup lang="ts">
import type {
    CommentEntityKind,
    CommentPreview,
    CommentReactionType,
} from '@/entities/comment';
import { computed } from 'vue';

import { useEntityComments } from '../lib/useEntityComments';

const props = withDefaults(
    defineProps<{
        entityKind: CommentEntityKind;
        entityId: number;
        reloadProp: 'plant' | 'recipe';
        comments?: CommentPreview[];
        commentsCount?: number;
        isAuthenticated?: boolean;
        currentUserId?: number | null;
        isAdmin?: boolean;
        heading?: string;
    }>(),
    {
        comments: () => [],
        commentsCount: 0,
        isAuthenticated: false,
        currentUserId: null,
        isAdmin: false,
        heading: 'Комментарии',
    },
);

const {
    busy,
    draft,
    editingId,
    editDraft,
    submitNew,
    startEdit,
    cancelEdit,
    submitEdit,
    destroy,
    reactionBusyId,
    toggleReaction,
} = useEntityComments({
    entityKind: props.entityKind,
    entityId: props.entityId,
    reloadProp: props.reloadProp,
});

const reactionOptions: {
    type: CommentReactionType;
    emoji: string;
    label: string;
}[] = [
    { type: 'like', emoji: '👍', label: 'Нравится' },
    { type: 'heart', emoji: '❤️', label: 'Сердце' },
    { type: 'plant', emoji: '🌱', label: 'Полезно' },
];

const count = computed(() =>
    props.commentsCount > 0 ? props.commentsCount : props.comments.length,
);

function authorLabel(comment: CommentPreview): string {
    return comment.author.name?.trim() || comment.author.username;
}

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

function canModerate(comment: CommentPreview): boolean {
    if (!props.isAuthenticated) {
        return false;
    }

    if (props.isAdmin) {
        return true;
    }

    return props.currentUserId === comment.author.id;
}

function reactionCount(comment: CommentPreview, type: CommentReactionType): number {
    return comment.reactions?.find((reaction) => reaction.type === type)?.count ?? 0;
}
</script>

<template>
    <section class="entity-comments" aria-labelledby="entity-comments-heading">
        <header class="entity-comments__header">
            <h2 id="entity-comments-heading" class="entity-comments__title">
                {{ heading }}
            </h2>
            <span class="entity-comments__count" aria-live="polite">
                {{ count }}
            </span>
        </header>

        <p v-if="comments.length === 0" class="entity-comments__empty">
            Пока нет комментариев.
        </p>

        <ul v-else class="entity-comments__list">
            <li
                v-for="comment in comments"
                :key="comment.id"
                class="entity-comments__item"
            >
                <div class="entity-comments__meta">
                    <span class="entity-comments__author">
                        {{ authorLabel(comment) }}
                    </span>
                    <time
                        class="entity-comments__time"
                        :datetime="comment.created_at"
                    >
                        {{ formatDate(comment.created_at) }}
                    </time>
                    <div
                        v-if="canModerate(comment) && editingId !== comment.id"
                        class="entity-comments__actions"
                    >
                        <button
                            type="button"
                            class="entity-comments__action"
                            :disabled="busy"
                            @click="startEdit(comment.id, comment.content)"
                        >
                            Изменить
                        </button>
                        <button
                            type="button"
                            class="entity-comments__action entity-comments__action--danger"
                            :disabled="busy"
                            @click="destroy(comment.id)"
                        >
                            Удалить
                        </button>
                    </div>
                </div>

                <template v-if="editingId === comment.id">
                    <textarea
                        v-model="editDraft"
                        class="entity-comments__textarea"
                        rows="3"
                        :disabled="busy"
                    />
                    <div class="entity-comments__edit-actions">
                        <button
                            type="button"
                            class="entity-comments__submit"
                            :disabled="busy || editDraft.trim() === ''"
                            @click="submitEdit"
                        >
                            Сохранить
                        </button>
                        <button
                            type="button"
                            class="entity-comments__cancel"
                            :disabled="busy"
                            @click="cancelEdit"
                        >
                            Отмена
                        </button>
                    </div>
                </template>
                <p v-else class="entity-comments__content">
                    {{ comment.content }}
                </p>
                <div class="entity-comments__reactions" aria-label="Реакции на комментарий">
                    <button
                        v-for="reaction in reactionOptions"
                        :key="reaction.type"
                        type="button"
                        class="entity-comments__reaction"
                        :class="{ 'entity-comments__reaction--active': comment.user_reaction === reaction.type }"
                        :aria-label="`${reaction.label}: ${reactionCount(comment, reaction.type)}`"
                        :aria-pressed="comment.user_reaction === reaction.type"
                        :title="isAuthenticated ? reaction.label : 'Войдите, чтобы оставить реакцию'"
                        :disabled="!isAuthenticated || busy || reactionBusyId === comment.id"
                        @click="toggleReaction(comment.id, reaction.type, comment.user_reaction)"
                    >
                        <span aria-hidden="true">{{ reaction.emoji }}</span>
                        <span>{{ reactionCount(comment, reaction.type) }}</span>
                    </button>
                </div>
            </li>
        </ul>

        <form
            v-if="isAuthenticated"
            class="entity-comments__form"
            aria-label="Новый комментарий"
            @submit.prevent="submitNew"
        >
            <label class="entity-comments__form-label">
                <span class="entity-comments__form-caption">Ваш комментарий</span>
                <textarea
                    v-model="draft"
                    class="entity-comments__textarea"
                    rows="3"
                    :disabled="busy"
                    placeholder="Напишите отзыв или вопрос"
                />
            </label>
            <button
                type="submit"
                class="entity-comments__submit"
                :disabled="busy || draft.trim() === ''"
            >
                Отправить
            </button>
        </form>
        <p v-else class="entity-comments__guest-hint">
            Войдите в аккаунт, чтобы комментировать и оставлять реакции.
        </p>
    </section>
</template>

<style scoped>
.entity-comments {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    padding: 1.25rem;
    border-radius: 1rem;
    border: 1px solid #d7e5e0;
    background: #fff;
}

.entity-comments__header {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
}

.entity-comments__title {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: #203f39;
}

.entity-comments__count {
    font-size: 0.875rem;
    font-weight: 500;
    color: #78928a;
}

.entity-comments__empty,
.entity-comments__guest-hint {
    margin: 0;
    font-size: 0.875rem;
    color: #5a746c;
    line-height: 1.45;
}

.entity-comments__list {
    margin: 0;
    padding: 0;
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.entity-comments__item {
    padding-bottom: 1rem;
    border-bottom: 1px solid #e8f0ed;
}

.entity-comments__item:last-child {
    padding-bottom: 0;
    border-bottom: none;
}

.entity-comments__meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.35rem 0.75rem;
    margin-bottom: 0.35rem;
    font-size: 0.8125rem;
}

.entity-comments__author {
    font-weight: 600;
    color: #21483f;
}

.entity-comments__time {
    color: #78928a;
}

.entity-comments__actions {
    display: flex;
    gap: 0.5rem;
    margin-left: auto;
}

.entity-comments__action {
    padding: 0;
    border: none;
    background: none;
    font: inherit;
    font-size: 0.75rem;
    color: #3d6b61;
    cursor: pointer;
    text-decoration: underline;
}

.entity-comments__action:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.entity-comments__action--danger {
    color: #b4513c;
}

.entity-comments__content {
    margin: 0;
    font-size: 0.9375rem;
    line-height: 1.5;
    color: #203f39;
    white-space: pre-wrap;
}

.entity-comments__reactions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
    margin-top: 0.65rem;
}

.entity-comments__reaction {
    display: inline-flex;
    min-width: 2.5rem;
    align-items: center;
    justify-content: center;
    gap: 0.3rem;
    padding: 0.25rem 0.5rem;
    border: 1px solid #d7e5e0;
    border-radius: 999px;
    background: #fff;
    color: #46645a;
    font: inherit;
    font-size: 0.8rem;
    cursor: pointer;
}

.entity-comments__reaction--active {
    border-color: #81aa9d;
    background: #eaf4ef;
    color: #21483f;
}

.entity-comments__reaction:disabled {
    cursor: default;
    opacity: 0.75;
}

.entity-comments__form {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-top: 0.25rem;
    padding-top: 1rem;
    border-top: 1px dashed #d7e5e0;
}

.entity-comments__form-label {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}

.entity-comments__form-caption {
    font-size: 0.8125rem;
    font-weight: 500;
    color: #78928a;
}

.entity-comments__textarea {
    width: 100%;
    resize: vertical;
    padding: 0.65rem 0.75rem;
    border-radius: 0.5rem;
    border: 1px solid #d7e5e0;
    font: inherit;
    color: #203f39;
    background: #fff;
}

.entity-comments__submit {
    align-self: flex-start;
    padding: 0.45rem 1rem;
    border-radius: 0.5rem;
    border: none;
    background: #2f6d61;
    color: #fff;
    font: inherit;
    font-size: 0.875rem;
    cursor: pointer;
}

.entity-comments__submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.entity-comments__cancel {
    padding: 0.45rem 0.75rem;
    border: none;
    background: none;
    font: inherit;
    font-size: 0.875rem;
    color: #78928a;
    cursor: pointer;
}

.entity-comments__edit-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.35rem;
}
</style>
