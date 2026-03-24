<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import SecurityController from '@/actions/App/Http/Controllers/Settings/SecurityController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import type { BreadcrumbItem } from '@/types';

const title = 'Wachtwoord';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: title,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head :title="title" />

        <SettingsLayout>
            <div class="space-y-6">
                <Heading variant="small" title="Wachtwoord bijwerken" description="Zorg ervoor dat je account een lang, willekeurig wachtwoord gebruikt om veilig te blijven" />

                <Form v-bind="SecurityController.update.form()" :options="{
                    preserveScroll: true,
                }" reset-on-success :reset-on-error="[
                    'password',
                    'password_confirmation',
                    'current_password',
                ]" class="space-y-6" v-slot="{ errors, processing, recentlySuccessful }">
                    <div class="grid gap-2">
                        <Label for="current_password">Huidig wachtwoord</Label>
                        <PasswordInput id="current_password" name="current_password" class="mt-1 block w-full" autocomplete="current-password" placeholder="Huidig wachtwoord" />
                        <InputError :message="errors.current_password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password">Nieuw wachtwoord</Label>
                        <PasswordInput id="password" name="password" class="mt-1 block w-full" autocomplete="new-password" placeholder="Nieuw wachtwoord" />
                        <InputError :message="errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password_confirmation">Bevestig wachtwoord</Label>
                        <PasswordInput id="password_confirmation" name="password_confirmation" class="mt-1 block w-full" autocomplete="new-password" placeholder="Bevestig wachtwoord" />
                        <InputError :message="errors.password_confirmation" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="processing" data-test="update-password-button">Wachtwoord opslaan</Button>

                        <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0" leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                            <p v-show="recentlySuccessful" class="text-sm text-neutral-600">
                                Opgeslagen.
                            </p>
                        </Transition>
                    </div>
                </Form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
