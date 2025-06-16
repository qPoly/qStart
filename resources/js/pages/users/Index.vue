<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import type { User } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Plus } from 'lucide-vue-next';

interface Props {
    users: User[];
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Gebruikers',
        href: route('users.index'),
    },
];
</script>

<template>

    <Head title="Gebruikers" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="flex justify-between mb-4">
                <h1 class="text-2xl font-bold">Gebruikers</h1>
                <Button @click="router.visit(route('users.create'))">
                    <Plus />
                    Gebruiker toevoegen
                </Button>
            </div>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Naam</TableHead>
                        <TableHead>E-mailadres</TableHead>
                        <TableHead>Aangemaakt op</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="user in users" :key="user.id" @click="router.visit(route('users.edit', user.id))">
                        <TableCell>{{ user.name }}</TableCell>
                        <TableCell>{{ user.email }}</TableCell>
                        <TableCell>{{ new Date(user.created_at).toLocaleString('nl-NL') }}</TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </AppLayout>
</template>