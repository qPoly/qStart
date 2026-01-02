<script setup lang="ts">
import FileUpload from '@/components/FileUpload.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { destroy, index } from '@/routes/organisations';
import { BreadcrumbItem, Organisation } from '@/types';
import { Head, useForm, router, Form } from '@inertiajs/vue3';
import { Plus, Save, Trash2, X } from 'lucide-vue-next';

interface Props {
    organisation?: Organisation;
}

const props = defineProps<Props>();
const title = props.organisation ? 'Organisatie aanpassen' : 'Organisatie toevoegen';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Organisaties',
        href: index.url({ mergeQuery: {} }),
    },
    {
        title: title,
    },
];

const form = useForm({
    _method: props.organisation?.id ? 'PUT' : undefined,
    name: props.organisation?.name || '',
    logo_path: props.organisation?.logo_path || '',
});

const submit = () => {
    if (props.organisation?.id) {
        form.post(`/organisations/${props.organisation.id}`);
    } else {
        form.post('/organisations');
    }
};
</script>

<template>

    <Head :title="title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-sm p-4">
            <div class="mb-4 flex justify-between">
                <h1 class="text-2xl font-bold">
                    {{ title }}
                </h1>
            </div>

            <form @submit.prevent="submit()" class="space-y-6">
                <div class="space-y-2">
                    <Label for="name">Naam</Label>
                    <Input id="name" name="name" type="text" v-model="form.name" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="space-y-2">
                    <FileUpload v-model="form.logo_path" name="logo_path" />
                    <InputError class="mt-2" :message="form.errors.logo_path" />
                </div>

                <div class="flex justify-end gap-4">
                    <Button type="button" variant="outline" @click="router.visit(index.url({ mergeQuery: {} }))">
                        <X />
                        Annuleren
                    </Button>

                    <Dialog v-if="organisation">
                        <DialogTrigger asChild>
                            <Button variant="destructive" type="button">
                                <Trash2 />
                            </Button>
                        </DialogTrigger>
                        <DialogContent>
                            <Form :action="destroy(organisation!.id, { mergeQuery: {} })" #default="{ processing }">
                                <DialogHeader>
                                    <DialogTitle>Organisatie verwijderen</DialogTitle>
                                    <DialogDescription>
                                        Weet je zeker dat je deze organisatie wilt verwijderen?
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

                    <Button type="submit" :disabled="form.processing">
                        <component :is="organisation ? Save : Plus" />
                        {{ organisation ? 'Opslaan' : 'Toevoegen' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>