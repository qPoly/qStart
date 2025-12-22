<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { Pagination, PaginationContent, PaginationEllipsis, PaginationItem, PaginationNext, PaginationPrevious } from '@/components/ui/pagination';

interface Props {
    data: {
        from: number;
        to: number;
        total: number;
        per_page: number;
        current_page: number;
        links: {
            url: string | null;
            label: string;
            active: boolean;
        }[];
    };
}

defineProps<Props>();
</script>

<template>
    <div class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">
            <template v-if="data.total > 0">
                {{ data.from }} t/m {{ data.to }} van {{ data.total }} resultaten
            </template>
            <template v-else>
                Geen resultaten
            </template>
        </div>

        <Pagination :items-per-page="data.per_page" :total="data.total" :default-page="data.current_page">
            <PaginationContent>
                <div v-for="link of data.links" :key="link.label">
                    <template v-if="link.url">
                        <PaginationPrevious v-if="link.label == '[icon-left]'" @click="router.visit(link.url)" />
                        <PaginationNext v-else-if="link.label == '[icon-right]'" @click="router.visit(link.url)" />
                        <PaginationItem v-else-if="link.url" :value="0" :is-active="link.active" @click="router.visit(link.url)">
                            {{ link.label }}
                        </PaginationItem>
                    </template>
                    <template v-else>
                        <PaginationPrevious v-if="link.label == '[icon-left]'" />
                        <PaginationNext v-else-if="link.label == '[icon-right]'" />
                        <PaginationEllipsis v-else-if="link.label == '...'" />
                        <PaginationItem v-else-if="link.url" :value="0" :is-active="link.active">
                            {{ link.label }}
                        </PaginationItem>
                    </template>
                </div>
            </PaginationContent>
        </Pagination>
    </div>
</template>