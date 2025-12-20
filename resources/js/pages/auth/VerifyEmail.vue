<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { logout } from '@/routes';
import { send } from '@/routes/verification';
import { Form, Head } from '@inertiajs/vue3';

defineProps<{
    status?: string;
}>();
</script>

<template>
    <AuthLayout title="E-mail verifiëren" description="Verifieer je e-mailadres door op de link te klikken die we je net hebben gemaild.">

        <Head title="E-mail verificatie" />

        <div v-if="status === 'verification-link-sent'" class="mb-4 text-center text-sm font-medium text-green-600">
            Er is een nieuwe verificatielink verzonden naar het e-mailadres dat je hebt opgegeven tijdens de registratie.
        </div>

        <Form v-bind="send.form()" class="space-y-6 text-center" v-slot="{ processing }">
            <Button :disabled="processing" variant="secondary">
                <Spinner v-if="processing" />
                Verificatie-e-mail opnieuw versturen
            </Button>

            <TextLink :href="logout()" as="button" class="mx-auto block text-sm">
                Uitloggen
            </TextLink>
        </Form>
    </AuthLayout>
</template>
