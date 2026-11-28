import type { CommentPreview } from '@/entities/comment';

export type RecipePlantSummary = {
    id: number;
    name: string;
};

export type RecipeTagSummary = {
    id: number;
    name: string;
    category: string;
};

export type RecipeCardItem = {
    id: number;
    name: string;
    description: string | null;
    healthy: boolean;
    is_favorited?: boolean;
    plants: RecipePlantSummary[];
};

export type FavoriteRecipeItem = RecipeCardItem & { is_favorited: true };

export type RecipeSection = {
    after: number;
    recipes: RecipeCardItem[];
};

export type RecipeDetail = {
    id: number;
    name: string;
    description: string | null;
    additional: string | null;
    advice: string | null;
    recipe: string;
    healthy: boolean;
    is_favorited?: boolean;
    plants?: RecipePlantSummary[];
    tags?: RecipeTagSummary[];
    comments?: CommentPreview[];
    comments_count?: number;
};

export type RecipeShowPageProps = {
    recipe: RecipeDetail;
    auth: {
        user: import('@/entities/user').AuthUser | null;
    };
};
