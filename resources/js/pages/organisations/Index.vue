<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { debounce } from 'es-toolkit';
import { ArrowDownAz, ArrowUpAz, ArrowUpDown, Plus, Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import PagePreferencesComponent from '@/components/PagePreferences.vue';
import Pagination from '@/components/Pagination.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, edit, index } from '@/routes/organisations';
import type { BreadcrumbItem, Column, UserPreferences, Organisation } from '@/types';

interface Props {
    organisations: {
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
        data: Organisation[];
    };
    filters: {
        search?: string;
    };
    userPreferences: UserPreferences;
}

const props = defineProps<Props>();
const title = 'Organisaties';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: title,
    },
];

const pagePrefs = ref<UserPreferences>(props.userPreferences);
const search = ref(props.filters.search || '');

const performSearch = debounce((value: string) => {
    router.get(
        index.url({ mergeQuery: {} }),
        { search: value || undefined },
        { preserveState: true, preserveScroll: true }
    );
}, 300);

const performSort = debounce((column: Column) => {
    if (column.unsortable) {
        return;
    }

    router.get(index.url({ mergeQuery: {} }), {
        sortColumn: column.key,
        sortDirection: pagePrefs.value.sortColumn === column.key && pagePrefs.value.sortDirection === 'asc' ? 'desc' : 'asc'
    }, { replace: true })
}, 300);

watch(search, (value) => {
    performSearch(value);
});
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
                    <PagePreferencesComponent page="organisations" v-model="pagePrefs" />
                    <Button @click="router.visit(create.url({ mergeQuery: {} }))">
                        <Plus />
                        Organisatie toevoegen
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
                    <TableRow v-for="organisation in organisations.data" :key="organisation.id">
                        <TableCell v-for="column in pagePrefs.columns" :key="column.key" v-show="column.visible" :class="{ 'py-0': column.key == 'logo' }" @click="router.visit(edit.url(organisation.id, { mergeQuery: {} }))">
                            <img v-if="column.key == 'logo' && organisation.logo_path" :src="'/storage/' + organisation.logo_path" :alt="organisation.name" />

                            <template v-if="['created_at', 'updated_at'].includes(column.key)">
                                {{ organisation[column.key as keyof Organisation] ? new Date(organisation[column.key as keyof Organisation] as string).toLocaleString('nl-NL') : '' }}
                            </template>

                            <template v-else>
                                {{ organisation[column.key as keyof Organisation] }}
                            </template>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <Pagination :data="organisations" class="mt-4" />
        </div>
    </AppLayout>
</template>