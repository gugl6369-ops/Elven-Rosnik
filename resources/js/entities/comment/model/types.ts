export type CommentAuthor = {
    id: number;
    username: string;
    name: string | null;
    avatar: string | null;
};

export type CommentReactionType = 'like' | 'heart' | 'plant';

export type CommentReactionSummary = {
    type: CommentReactionType;
    count: number;
};

export type CommentPreview = {
    id: number;
    content: string;
    created_at: string;
    parent_id?: number | null;
    author: CommentAuthor;
    reactions?: CommentReactionSummary[];
    user_reaction?: CommentReactionType | null;
};

export type CommentEntityKind = 'plant' | 'recipe';
