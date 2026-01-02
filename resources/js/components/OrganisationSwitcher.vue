<script setup lang="ts">
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger, DropdownMenuGroup, DropdownMenuItem, DropdownMenuSeparator } from '@/components/ui/dropdown-menu';
import { SidebarMenu, SidebarMenuButton, SidebarMenuItem, useSidebar } from '@/components/ui/sidebar';
import { switchMethod } from '@/routes/organisations';
import { Organisation } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronsUpDown } from 'lucide-vue-next';

const page = usePage();
const user = page.props.auth.user;
const organisations = page.props.organisations as Organisation[];

const { isMobile, state } = useSidebar();
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem>
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <SidebarMenuButton size="lg" class="cursor-pointer data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground" data-test="sidebar-menu-button">
                        <div class="grid flex-1 text-left text-sm leading-tight">
                            <span class="truncate font-medium">{{ user?.organisation?.name || 'Beheerder' }}</span>
                        </div>
                        <ChevronsUpDown class="ml-auto size-4" />
                    </SidebarMenuButton>
                </DropdownMenuTrigger>
                <DropdownMenuContent class="w-(--reka-dropdown-menu-trigger-width) min-w-56 rounded-lg" :side="isMobile
                    ? 'bottom'
                    : state === 'collapsed'
                        ? 'left'
                        : 'bottom'
                    " align="end" :side-offset="4">
                    <DropdownMenuGroup>
                        <DropdownMenuItem :as-child="true" v-for="organisation in organisations" :key="organisation.id">
                            <Link class="block w-full" :class="{ 'bg-accent': user.organisation?.id === organisation.id }" :href="switchMethod(organisation.id)">
                                {{ organisation.name }}
                            </Link>
                        </DropdownMenuItem>
                    </DropdownMenuGroup>

                    <DropdownMenuSeparator />

                    <DropdownMenuItem :as-child="true">
                        <Link class="block w-full" :class="{ 'bg-accent': !user.organisation?.id }" :href="switchMethod(0)">
                            Beheerder
                        </Link>
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </SidebarMenuItem>
    </SidebarMenu>
</template>
