<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};
</script>

<template>
    <AuthLayout title="E-mail verifiëren" description="Verifieer je e-mailadres door op de link te klikken die we je net hebben gemaild.">

        <Head title="E-mail verificatie" />

        <div v-if="status === 'verification-link-sent'" class="mb-4 text-center text-sm font-medium text-green-600">
            Er is een nieuwe verificatielink verzonden naar het e-mailadres dat je hebt opgegeven tijdens de registratie.
        </div>

        <form @submit.prevent="submit" class="space-y-6 text-center">
            <Button :disabled="form.processing" variant="secondary">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                Verificatie e-mail opnieuw versturen
            </Button>

            <TextLink :href="route('logout')" method="post" as="button" class="mx-auto block text-sm">Uitloggen</TextLink>
        </form>
    </AuthLayout>
</template>
