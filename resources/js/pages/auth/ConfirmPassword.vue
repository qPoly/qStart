<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <AuthLayout title="Bevestig je wachtwoord" description="Dit is een beveiligd gedeelte van de applicatie. Bevestig je wachtwoord voordat je verder gaat.">

        <Head title="Wachtwoord bevestigen" />

        <form @submit.prevent="submit">
            <div class="space-y-6">
                <div class="grid gap-2">
                    <Label htmlFor="password">Wachtwoord</Label>
                    <Input id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="current-password" autofocus />

                    <InputError :message="form.errors.password" />
                </div>

                <div class="flex items-center">
                    <Button class="w-full" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                        Wachtwoord bevestigen
                    </Button>
                </div>
            </div>
        </form>
    </AuthLayout>
</template>
