export type StoryAuthor = {
    id: number;
    username: string;
    name: string | null;
    avatar: string | null;
};

export type StoryListItem = {
    id: number;
    media_url: string;
    media_type: 'image' | 'video';
    caption: string | null;
    label: string;
    link_url: string | null;
    author: StoryAuthor | null;
};

export type HomePageProps = {
    appName?: string;
    stories: StoryListItem[];
    plants: import('@/entities/plant').Paginated<import('@/entities/plant').PlantListItem>;
    recipeSections: import('@/entities/recipe').RecipeSection[];
    auth: {
        user: import('@/entities/user').AuthUser | null;
    };
};
