import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    organisations: Organisation[];
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href?: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    auth: Auth;
    sidebarOpen: boolean;
};

export interface UserPreferences {
    columns?: Column[];
    sortColumn?: string;
    sortDirection?: 'asc' | 'desc';
    perPage?: number;
}

export interface Column {
    key: string;
    label: string;
    width?: number;
    visible?: boolean;
    unsortable?: boolean;
}

export interface Role {
    id: number;
    name: string;
}

export interface Organisation {
    id: number;
    name: string;
    logo_path: string | null;
}

export interface User {
    id: number;
    organisation_id: number | null;
    organisation?: Organisation;
    name: string;
    email: string;
    avatar: string | null;
    roles: Role[];
    permissions: string[];
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;
