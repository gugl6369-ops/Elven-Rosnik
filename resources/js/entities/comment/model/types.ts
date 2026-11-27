export type CommentAuthor = {
    id: number;
    username: string;
    name: string | null;
    avatar: string | null;
};

export type CommentPreview = {
    id: number;
    content: string;
    created_at: string;
    parent_id?: number | null;
    author: CommentAuthor;
};

export type CommentEntityKind = 'plant' | 'recipe';