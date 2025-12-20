<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { Form } from '@inertiajs/vue3';
import { useTemplateRef } from 'vue';

// Components
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const passwordInput = useTemplateRef('passwordInput');
</script>

<template>
    <div class="space-y-6">
        <HeadingSmall title="Account verwijderen" description="Verwijder je account en alle bijbehorende gegevens" />
        <div class="space-y-4 rounded-lg border border-red-100 bg-red-50 p-4 dark:border-red-200/10 dark:bg-red-700/10">
            <div class="relative space-y-0.5 text-red-600 dark:text-red-100">
                <p class="font-medium">Waarschuwing</p>
                <p class="text-sm">Wees voorzichtig, deze actie kan niet ongedaan worden gemaakt.</p>
            </div>
            <Dialog>
                <DialogTrigger as-child>
                    <Button variant="destructive" data-test="delete-user-button">Account verwijderen</Button>
                </DialogTrigger>
                <DialogContent>
                    <Form v-bind="ProfileController.destroy.form()" reset-on-success @error="() => passwordInput?.$el?.focus()" :options="{
                        preserveScroll: true,
                    }" class="space-y-6" v-slot="{ errors, processing, reset, clearErrors }">
                        <DialogHeader class="space-y-3">
                            <DialogTitle>Weet je zeker dat je je account wilt verwijderen?</DialogTitle>
                            <DialogDescription>
                                Zodra je account is verwijderd, worden alle gegevens en resources permanent verwijderd. Voer je wachtwoord in om te bevestigen dat je je account permanent wilt verwijderen.
                            </DialogDescription>
                        </DialogHeader>

                        <div class="grid gap-2">
                            <Label for="password" class="sr-only">Wachtwoord</Label>
                            <Input id="password" type="password" name="password" ref="passwordInput" placeholder="Wachtwoord" />
                            <InputError :message="errors.password" />
                        </div>

                        <DialogFooter class="gap-2">
                            <DialogClose as-child>
                                <Button variant="secondary" @click="() => { clearErrors(); reset(); }">
                                    Annuleren
                                </Button>
                            </DialogClose>

                            <Button type="submit" variant="destructive" :disabled="processing" data-test="confirm-delete-user-button">
                                Account verwijderen
                            </Button>
                        </DialogFooter>
                    </Form>
                </DialogContent>
            </Dialog>
        </div>
    </div>
</template>
