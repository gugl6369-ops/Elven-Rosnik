import type { CommentEntityKind } from '@/entities/comment';
import { router } from '@inertiajs/vue3';
import { ref, type Ref } from 'vue';

import { useAppToast } from '@/shared/lib/toast';

import {
    commentDestroyUrl,
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
    };
}