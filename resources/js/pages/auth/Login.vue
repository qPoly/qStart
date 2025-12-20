<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
</script>

<template>
    <AuthBase title="Log in op je account" description="Voer je e-mailadres en wachtwoord in om in te loggen">

        <Head title="Inloggen" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <Form v-bind="store.form()" :reset-on-success="['password']" v-slot="{ errors, processing }" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email">E-mailadres</Label>
                    <Input id="email" type="email" name="email" required autofocus :tabindex="1" autocomplete="email" placeholder="email@voorbeeld.nl" />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Wachtwoord</Label>
                        <TextLink v-if="canResetPassword" :href="request()" class="text-sm" :tabindex="5">
                            Wachtwoord vergeten?
                        </TextLink>
                    </div>
                    <Input id="password" type="password" name="password" required :tabindex="2" autocomplete="current-password" placeholder="Wachtwoord" />
                    <InputError :message="errors.password" />
                </div>

                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" name="remember" :tabindex="3" :default-value="true" />
                        <span>Onthoud mij</span>
                    </Label>
                </div>

                <Button type="submit" class="mt-4 w-full" :tabindex="4" :disabled="processing" data-test="login-button">
                    <Spinner v-if="processing" />
                    Inloggen
                </Button>
            </div>
        </Form>
    </AuthBase>
</template>
