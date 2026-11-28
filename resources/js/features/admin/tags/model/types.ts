export type AdminTagOption = { id: number; name: string; category: string };

export type TagCategory =
    | 'эффекты'
    | 'противопоказания'
    | 'возможные применения';

export type PendingTag = { name: string; category: TagCategory };

