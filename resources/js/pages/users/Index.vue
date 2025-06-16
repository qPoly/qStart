<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import type { User } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Plus } from 'lucide-vue-next';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';

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

const { getInitials } = useInitials();
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
                        <TableHead></TableHead>
                        <TableHead>Naam</TableHead>
                        <TableHead>E-mailadres</TableHead>
                        <TableHead>Aangemaakt op</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="user in users" :key="user.id" @click="router.visit(route('users.edit', user.id))">
                        <TableCell class="w-0">
                            <Avatar class="h-8 w-8 overflow-hidden rounded-lg">
                                <AvatarImage v-if="user.avatar && user.avatar !== ''" :src="user.avatar!" :alt="user.name" />
                                <AvatarFallback class="rounded-lg text-black dark:text-white">
                                    {{ getInitials(user.name) }}
                                </AvatarFallback>
                            </Avatar>
                        </TableCell>
                        <TableCell>{{ user.name }}</TableCell>
                        <TableCell>{{ user.email }}</TableCell>
                        <TableCell>{{ new Date(user.created_at).toLocaleString('nl-NL') }}</TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </AppLayout>
</template>