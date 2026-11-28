import type { CommentEntityKind } from '@/entities/comment';

export function commentStoreUrl(
    kind: CommentEntityKind,
    entityId: number,
): string {
    return kind === 'plant'
        ? `/plants/${entityId}/comments`
        : `/recipes/${entityId}/comments`;
}

export function commentUpdateUrl(
    kind: CommentEntityKind,
    entityId: number,
    commentId: number,
): string {
    return kind === 'plant'
        ? `/plants/${entityId}/comments/${commentId}`
        : `/recipes/${entityId}/comments/${commentId}`;
}

export function commentDestroyUrl(
    kind: CommentEntityKind,
    entityId: number,
    commentId: number,
): string {
    return commentUpdateUrl(kind, entityId, commentId);
}

export function commentReactionUrl(
    kind: CommentEntityKind,
    entityId: number,
    commentId: number,
): string {
    return `${commentDestroyUrl(kind, entityId, commentId)}/reaction`;
}
