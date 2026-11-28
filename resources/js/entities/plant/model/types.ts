export type PlantGenus = {
    id: number;
    genus_name: string;
};

export type PlantListItem = {
    id: number;
    name: string;
    image_url: string | null;
    description: string | null;
    height: string | null;
    watering_frequency: string | null;
    sunlight: string | null;
    temp_min: string | null;
    temp_max: string | null;
    genus: PlantGenus | null;
    is_favorited?: boolean;
};

export type PlantTag = {
    id: number;
    name: string;
    category: string;
};

export type PlantRecipeSummary = {
    id: number;
    name: string;
};

import type { CommentPreview } from '@/entities/comment';

/** @deprecated Use `CommentPreview` from `@/entities/comment`. */
export type PlantCommentPreview = CommentPreview;

export type PlantDetail = {
    id: number;
    name: string;
    image_url: string | null;
    description: string | null;
    height: string | null;
    watering_frequency: string | null;
    sunlight: string | null;
    temp_min: string | null;
    temp_max: string | null;
    preparation?: string | null;
    storage?: string | null;
    collection?: string | null;
    readiness_check?: string | null;
    drying?: string | null;
    genus?: PlantGenus | null;
    genus_name?: string | null;
    tags?: PlantTag[];
    recipes?: PlantRecipeSummary[];
    comments_count?: number;
    comments?: PlantCommentPreview[];
    is_favorited?: boolean;
    favorites_count?: number;
};

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type Paginated<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: PaginationLink[];
    prev_page_url: string | null;
    next_page_url: string | null;
};

export type CatalogListFilters = {
    search: string;
};

export type CatalogPageProps = {
    plants: Paginated<PlantListItem>;
    filters?: CatalogListFilters;
    auth: {
        user: import('@/entities/user').AuthUser | null;
    };
};

export type FavoritesPageProps = {
    plants: Paginated<PlantListItem>;
    recipes: Paginated<import('@/entities/recipe').FavoriteRecipeItem>;
    auth: {
        user: import('@/entities/user').AuthUser | null;
    };
};

export type PlantShowPageProps = {
    plant: PlantDetail;
    auth: {
        user: import('@/entities/user').AuthUser | null;
    };
};

