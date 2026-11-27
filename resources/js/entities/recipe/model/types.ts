import type { CommentPreview } from '@/entities/comment';

export type RecipePlantSummary = {
    id: number;
    name: string;
};

export type RecipeDetail = {
    id: number;
    name: string;
    description: string | null;
    additional: string | null;
    advice: string | null;
    recipe: string;
    healthy: boolean;
    plants?: RecipePlantSummary[];
    comments?: CommentPreview[];
    comments_count?: number;
};

export type RecipeShowPageProps = {
    recipe: RecipeDetail;
    auth: {
        user: import('@/entities/user').AuthUser | null;
    };
};