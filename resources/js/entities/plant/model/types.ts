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

/**
 * Row for the read-only comments block on the plant detail page.
 * Expected from Inertia when the backend exposes `plant.comments`.
 */
export type PlantCommentPreview = {
    id: number;
    content: string;
    created_at: string;
    author: {
        id: number;
        username: string;
        name: string | null;
    };
};

/**
 * Detail DTO for `GET /catalog/plants/{plant}` (target props shape).
 * Partial props from an older controller revision are normalized in `PlantDetail` widget.
 */
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
    /** Legacy flat field from partial backend payloads */
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

export type CatalogPageProps = {
    plants: Paginated<PlantListItem>;
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