import type { Organisation } from "./organisation";

export type User = {
    id: number;
    organisation_id: number | null;
    organisation?: Organisation;
    name: string;
    email: string;
    avatar?: string;
    roles: Role[];
    permissions: string[];
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
};

export type Role = {
    id: number;
    name: string;
}

export type Auth = {
    organisations: Organisation[];
    user: User;
};

export type TwoFactorConfigContent = {
    title: string;
    description: string;
    buttonText: string;
};
