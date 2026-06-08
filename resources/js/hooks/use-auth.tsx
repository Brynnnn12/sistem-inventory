import { usePage } from '@inertiajs/react';
import type { SharedData } from '@/types';

export function useAuth() {
    const { auth, userRoles } = usePage<SharedData>().props;
    const user = auth.user;
    const roles: string[] = userRoles ?? [];

    const hasRole = (roleName: string) => roles.includes(roleName);

    return {
        user,
        roles,
        hasRole,
        isSuperAdmin: hasRole('super-admin'),
        isAdmin: hasRole('admin'),
    };
}
