export type Appearance = 'light' | 'dark' | 'system';
export type ResolvedAppearance = 'light' | 'dark';
export type AppVariant = 'header' | 'sidebar';

export type UserPreferences = {
    columns?: Column[];
    sortColumn?: string;
    sortDirection?: 'asc' | 'desc';
    perPage?: number;
}

export type Column = {
    key: string;
    label: string;
    width?: number;
    visible?: boolean;
    unsortable?: boolean;
}