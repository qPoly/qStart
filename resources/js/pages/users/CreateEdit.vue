<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { User } from '@/types';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Plus, Save, Trash2, X } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    user?: User;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Gebruikers',
        href: route('users.index'),
    },
    {
        title: props.user ? 'Gebruiker aanpassen' : 'Gebruiker toevoegen',
        href: props.user ? route('users.edit', props.user.id) : route('users.create'),
    },
];

const form = useForm({
    name: props.user?.name || '',
    email: props.user?.email || '',
    password: '',
    password_confirmation: '',
});

const isDeleteDialogOpen = ref(false);

const submit = () => {
    if (props.user) {
        form.put(route('users.update', props.user.id));
    } else {
        form.post(route('users.store'));
    }
};

const deleteUser = () => {
    form.delete(route('users.destroy', props.user!.id));
    isDeleteDialogOpen.value = false;
};
</script>

<template>

    <Head :title="user ? 'Gebruiker aanpassen' : 'Gebruiker toevoegen'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-md p-4">
            <div class="mb-4 flex justify-between">
                <h1 class="text-2xl font-bold">
                    {{ user ? 'Gebruiker aanpassen' : 'Gebruiker toevoegen' }}
                </h1>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="space-y-2">
                    <Label for="name">Naam</Label>
                    <Input id="name" v-model="form.name" type="text" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="space-y-2">
                    <Label for="email">E-mailadres</Label>
                    <Input id="email" v-model="form.email" type="email" autocomplete="off" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="space-y-2">
                    <Label for="password">{{ user ? 'Nieuw wachtwoord (optioneel)' : 'Wachtwoord' }}</Label>
                    <Input id="password" v-model="form.password" type="password" autocomplete="new-password" />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="space-y-2">
                    <Label for="password_confirmation">Wachtwoord bevestigen</Label>
                    <Input id="password_confirmation" v-model="form.password_confirmation" type="password" autocomplete="new-password" />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <div class="flex justify-end gap-4">
                    <Button type="button" variant="outline" @click="router.visit(route('users.index'))">
                        <X />
                        Annuleren
                    </Button>
                    <Dialog v-if="user" v-model:open="isDeleteDialogOpen">
                        <DialogTrigger asChild>
                            <Button variant="destructive">
                                <Trash2 />
                            </Button>
                        </DialogTrigger>
                        <DialogContent>
                            <DialogHeader>
                                <DialogTitle>Gebruiker verwijderen</DialogTitle>
                                <DialogDescription>
                                    Weet je zeker dat je deze gebruiker wilt verwijderen? Deze actie kan niet ongedaan worden gemaakt.
                                </DialogDescription>
                            </DialogHeader>
                            <DialogFooter class="mt-4 flex justify-end gap-4">
                                <Button variant="outline" @click="isDeleteDialogOpen = false">
                                    <X />
                                    Annuleren
                                </Button>
                                <Button variant="destructive" @click="deleteUser">
                                    <Trash2 />
                                    Verwijderen
                                </Button>
                            </DialogFooter>
                        </DialogContent>
                    </Dialog>
                    <Button type="submit" :disabled="form.processing">
                        <component :is="user ? Save : Plus" />
                        {{ user ? 'Opslaan' : 'Toevoegen' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
