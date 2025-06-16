<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <AuthLayout title="Wachtwoord vergeten" description="Voer je e-mailadres in om een wachtwoord reset link te ontvangen">

        <Head title="Wachtwoord vergeten" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <div class="space-y-6">
            <form @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="email">E-mailadres</Label>
                    <Input id="email" type="email" name="email" autocomplete="off" v-model="form.email" autofocus placeholder="email@voorbeeld.nl" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="my-6 flex items-center justify-start">
                    <Button class="w-full" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                        Stuur wachtwoord reset link
                    </Button>
                </div>
            </form>

            <div class="space-x-1 text-center text-sm text-muted-foreground">
                <span>Of ga terug naar</span>
                <TextLink :href="route('login')">inloggen</TextLink>
            </div>
        </div>
    </AuthLayout>
</template>
