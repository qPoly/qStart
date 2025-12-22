<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { can } from '@/composables/auth';
import AppLayout from '@/layouts/AppLayout.vue';
import { destroy, index, store, update } from '@/routes/users';
import type { Role, User } from '@/types';
import { type BreadcrumbItem } from '@/types';
import { Form, Head, router } from '@inertiajs/vue3';
import { Plus, Save, Trash2, X } from 'lucide-vue-next';

interface Props {
    roles: Role[];
    user?: User;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Gebruikers',
        href: index.url(),
    },
    {
        title: props.user ? 'Gebruiker aanpassen' : 'Gebruiker toevoegen',
    },
];
</script>

<template>

    <Head :title="user ? 'Gebruiker aanpassen' : 'Gebruiker toevoegen'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-sm p-4">
            <div class="mb-4 flex justify-between">
                <h1 class="text-2xl font-bold">
                    {{ user ? 'Gebruiker aanpassen' : 'Gebruiker toevoegen' }}
                </h1>
            </div>

            <Form :action="user ? update(user.id) : store()" #default="{ errors, processing }" class="space-y-6">
                <div class="space-y-2">
                    <Label for="name">Naam</Label>
                    <Input id="name" name="name" type="text" :default-value="user?.name" />
                    <InputError :message="errors.name" />
                </div>

                <div class="space-y-2" v-if="can('user.assign.role')">
                    <Label>Rol</Label>
                    <Select name="role" :default-value="user?.roles[0]?.name">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Selecteer een rol" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="role in roles" :key="role.id" :value="role.name">
                                {{ role.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors.role" />
                </div>

                <div class="space-y-2">
                    <Label for="email">E-mailadres</Label>
                    <Input id="email" name="email" type="email" autocomplete="off" :default-value="user?.email" />
                    <InputError :message="errors.email" />
                </div>

                <div class="space-y-2">
                    <Label for="password">{{ user ? 'Nieuw wachtwoord (optioneel)' : 'Wachtwoord' }}</Label>
                    <Input id="password" name="password" type="password" autocomplete="new-password" />
                    <InputError :message="errors.password" />
                </div>

                <div class="flex justify-end gap-4">
                    <Button type="button" variant="outline" @click="router.visit(index.url())">
                        <X />
                        Annuleren
                    </Button>

                    <Dialog v-if="user && can('user.delete')">
                        <DialogTrigger asChild>
                            <Button variant="destructive">
                                <Trash2 />
                            </Button>
                        </DialogTrigger>
                        <DialogContent>
                            <Form :action="destroy(user!.id)" #default="{ processing }">
                                <DialogHeader>
                                    <DialogTitle>Gebruiker verwijderen</DialogTitle>
                                    <DialogDescription>
                                        Weet je zeker dat je deze gebruiker wilt verwijderen? Deze actie kan niet ongedaan worden gemaakt.
                                    </DialogDescription>
                                </DialogHeader>
                                <DialogFooter class="mt-4 flex justify-end gap-4">
                                    <DialogClose as-child>
                                        <Button variant="outline" type="button">
                                            <X />
                                            Annuleren
                                        </Button>
                                    </DialogClose>
                                    <Button variant="destructive" type="submit" :disabled="processing">
                                        <Trash2 />
                                        Verwijderen
                                    </Button>
                                </DialogFooter>
                            </Form>
                        </DialogContent>
                    </Dialog>

                    <Button type="submit" :disabled="processing">
                        <component :is="user ? Save : Plus" />
                        {{ user ? 'Opslaan' : 'Toevoegen' }}
                    </Button>
                </div>
            </Form>
        </div>
    </AppLayout>
</template>
