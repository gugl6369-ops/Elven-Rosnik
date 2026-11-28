import type { CommentEntityKind, CommentReactionType } from '@/entities/comment';
import { router } from '@inertiajs/vue3';
import { ref, type Ref } from 'vue';

import { useAppToast } from '@/shared/lib/toast';

import {
    commentDestroyUrl,
    commentReactionUrl,
    commentStoreUrl,
    commentUpdateUrl,
} from './commentRoutes';

type UseEntityCommentsOptions = {
    entityKind: CommentEntityKind;
    entityId: number;
    reloadProp: 'plant' | 'recipe';
};

export function useEntityComments(options: UseEntityCommentsOptions) {
    const toast = useAppToast();
    const busy = ref(false);
    const draft = ref('');
    const editingId: Ref<number | null> = ref(null);
    const editDraft = ref('');
    const reactionBusyId: Ref<number | null> = ref(null);

    const reloadOptions = {
        preserveScroll: true,
        only: [options.reloadProp],
        onFinish: () => {
            busy.value = false;
        },
    };

    const submitNew = (): void => {
        const content = draft.value.trim();

        if (busy.value || content === '') {
            return;
        }

        busy.value = true;
        router.post(
            commentStoreUrl(options.entityKind, options.entityId),
            { content },
            {
                ...reloadOptions,
                onSuccess: () => {
                    draft.value = '';
                    toast.success('Комментарий добавлен');
                },
                onError: () => {
                    toast.error('Не удалось отправить комментарий');
                },
            },
        );
    };

    const startEdit = (commentId: number, content: string): void => {
        editingId.value = commentId;
        editDraft.value = content;
    };

    const cancelEdit = (): void => {
        editingId.value = null;
        editDraft.value = '';
    };

    const submitEdit = (): void => {
        const content = editDraft.value.trim();
        const commentId = editingId.value;

        if (busy.value || commentId === null || content === '') {
            return;
        }

        busy.value = true;
        router.patch(
            commentUpdateUrl(options.entityKind, options.entityId, commentId),
            { content },
            {
                ...reloadOptions,
                onSuccess: () => {
                    cancelEdit();
                    toast.success('Комментарий обновлён');
                },
                onError: () => {
                    toast.error('Не удалось сохранить изменения');
                },
            },
        );
    };

    const destroy = (commentId: number): void => {
        if (busy.value) {
            return;
        }

        if (!window.confirm('Удалить комментарий?')) {
            return;
        }

        busy.value = true;
        router.delete(
            commentDestroyUrl(options.entityKind, options.entityId, commentId),
            {
                ...reloadOptions,
                onSuccess: () => {
                    toast.success('Комментарий удалён');
                },
                onError: () => {
                    toast.error('Не удалось удалить комментарий');
                },
            },
        );
    };

    const toggleReaction = (
        commentId: number,
        reactionType: CommentReactionType,
        currentReaction: CommentReactionType | null | undefined,
    ): void => {
        if (busy.value || reactionBusyId.value !== null) return;

        reactionBusyId.value = commentId;
        const url = commentReactionUrl(options.entityKind, options.entityId, commentId);
        const requestOptions = {
            preserveScroll: true,
            preserveState: true,
            only: [options.reloadProp],
            onError: () => toast.error('Не удалось сохранить реакцию'),
            onFinish: () => {
                reactionBusyId.value = null;
            },
        };

        if (currentReaction === reactionType) {
            router.delete(url, requestOptions);
        } else {
            router.post(url, { reaction_type: reactionType }, requestOptions);
        }
    };

    return {
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
    };
}
