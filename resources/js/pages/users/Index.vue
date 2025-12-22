<script setup lang="ts">
import debounce from 'lodash/debounce';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useInitials } from '@/composables/useInitials';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Column, UserPreferences, User } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ArrowDownAz, ArrowUpAz, ArrowUpDown, Plus, Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import PagePreferencesComponent from '@/components/PagePreferences.vue';
import Pagination from '@/components/Pagination.vue';
import { Input } from '@/components/ui/input';
import { can } from '@/composables/auth';
import { create, edit, index } from '@/routes/users';

interface Props {
    users: {
        from: number;
        to: number;
        total: number;
        per_page: number;
        current_page: number;
        links: {
            url: string | null;
            label: string;
            active: boolean;
        }[];
        data: User[];
    };
    filters: {
        search?: string;
    };
    userPreferences: UserPreferences;
}

const props = defineProps<Props>();
const title = 'Gebruikers';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: title,
    },
];

const pagePrefs = ref<UserPreferences>(props.userPreferences);
const search = ref(props.filters.search || '');

const performSearch = debounce((value: string) => {
    router.get(
        index.url(),
        { search: value },
        { preserveState: true, preserveScroll: true }
    );
}, 300);

const performSort = debounce((column: Column) => {
    if (column.unsortable) return;

    router.get(index.url(), {
        sortColumn: column.key,
        sortDirection: pagePrefs.value.sortColumn === column.key && pagePrefs.value.sortDirection === 'asc' ? 'desc' : 'asc'
    }, { replace: true })
}, 300);

watch(search, (value) => {
    performSearch(value);
});

const { getInitials } = useInitials();
</script>

<template>

    <Head :title="title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="flex justify-between mb-4">
                <h1 class="text-2xl font-bold">
                    {{ title }}
                </h1>
                <div class="flex items-center gap-2">
                    <PagePreferencesComponent page="users" v-model="pagePrefs" />
                    <Button v-if="can('user.create')" @click="router.visit(create.url())">
                        <Plus />
                        Gebruiker toevoegen
                    </Button>
                </div>
            </div>

            <div class="mb-4">
                <div class="relative">
                    <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                    <Input v-model="search" type="search" placeholder="Zoeken..." class="pl-8" />
                </div>
            </div>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead v-for="column in pagePrefs.columns" :key="column.key" v-show="column.visible" :width="column.width">
                            <div class="flex gap-2 items-center" :class="{ 'cursor-pointer': !column.unsortable }" @click="performSort(column)">
                                {{ column.label }}
                                <template v-if="!column.unsortable">
                                    <ArrowUpDown v-if="pagePrefs.sortColumn != column.key" class="size-3.5" />
                                    <ArrowUpAz v-else-if="pagePrefs.sortDirection == 'asc'" class="size-3.5 text-foreground" />
                                    <ArrowDownAz v-else-if="pagePrefs.sortDirection == 'desc'" class="size-3.5 text-foreground" />
                                </template>
                            </div>
                        </TableHead>
                        <TableHead class="w-1"></TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="user in users.data" :key="user.id">
                        <TableCell v-for="column in pagePrefs.columns" :key="column.key" v-show="column.visible" @click="can('user.update') ? router.visit(edit.url(user.id)) : null">
                            <template v-if="column.key === 'avatar'">
                                <Avatar class="h-8 w-8 overflow-hidden rounded-lg">
                                    <AvatarImage v-if="user.avatar && user.avatar !== ''" :src="user.avatar!" :alt="user.name" />
                                    <AvatarFallback class="rounded-lg text-black dark:text-white">
                                        {{ getInitials(user.name) }}
                                    </AvatarFallback>
                                </Avatar>
                            </template>

                            <template v-if="column.key === 'role'">
                                <div class="comma-separated">
                                    <span v-for="role of user.roles" :key="role.id">{{ role.name }}</span>
                                </div>
                            </template>

                            <template v-if="['id', 'name', 'email'].includes(column.key)">
                                {{ user[column.key as keyof User] }}
                            </template>

                            <template v-if="['created_at', 'updated_at', 'email_verified_at'].includes(column.key)">
                                {{ user[column.key as keyof User] ? new Date(user[column.key as keyof User] as string).toLocaleString('nl-NL') : '' }}
                            </template>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <Pagination :data="users" class="mt-4" />
        </div>
    </AppLayout>
</template>
