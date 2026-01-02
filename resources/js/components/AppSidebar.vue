<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Building, LayoutGrid, Users } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { can } from '@/composables/auth';
import OrganisationSwitcher from './OrganisationSwitcher.vue';
import { dashboard } from '@/routes';
import users from '@/routes/users';
import organisations from '@/routes/organisations';

const page = usePage();
const user = page.props.auth.user;

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    ...can('manage users') ? [{
        title: 'Gebruikers',
        href: users.index(),
        icon: Users,
    }] : [],
    ...can('manage organisations') && !user.organisation?.id ? [{
        title: 'Organisaties',
        href: organisations.index(),
        icon: Building,
    }] : [],
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <OrganisationSwitcher v-if="can('manage organisations')" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
