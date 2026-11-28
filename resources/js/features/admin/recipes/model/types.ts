export type AdminRecipeRow = {
    id: number;
    name: string;
    description: string | null;
    additional: string | null;
    advice: string | null;
    recipe: string;
    healthy: boolean;
    plant_ids: number[];
    tag_ids: number[];
    plants?: { id: number; name: string }[];
};
