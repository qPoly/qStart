import { usePage } from "@inertiajs/vue3";

export const can = (permission: string) => {
    return usePage().props.auth.user.permissions.includes(permission);
}
