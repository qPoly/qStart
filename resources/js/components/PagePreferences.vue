<script setup lang="ts">
import axios from 'axios';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Checkbox } from '@/components/ui/checkbox';
import { GripVertical, Settings } from 'lucide-vue-next';
import { UserPreferences } from '@/types';
import Select from './ui/select/Select.vue';
import draggable from 'vuedraggable'
import { SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from './ui/select';

interface Props {
    page: string;
}

const props = defineProps<Props>();
const preferences = defineModel<UserPreferences>({ required: true });

const updatePreferences = () => {
    axios.put(route('pagePreferences.update', props.page), {
        columns: preferences.value.columns ? preferences.value.columns.map(column => ({ ...column })) : [],
        sortColumn: preferences.value.sortColumn,
        sortDirection: preferences.value.sortDirection,
        perPage: preferences.value.perPage,
    });
};
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="outline" size="icon">
                <Settings class="h-4 w-4" />
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" class="w-56 p-3">
            <h4 class="mb-2 text-sm font-medium">Kolommen</h4>
            <div class="space-y-2">
                <draggable v-model="preferences.columns" item-key="key" @end="updatePreferences()" handle=".handle">
                    <template #item="{ element: column }">
                        <div class="flex gap-1 items-center">
                            <GripVertical class="size-4 text-muted-foreground cursor-move handle" />
                            <label class="flex gap-2 items-center text-sm cursor-pointer select-none">
                                <Checkbox :id="column.key" v-model="column.visible" @update:model-value="updatePreferences()" />
                                {{ column.label }}
                            </label>
                        </div>
                    </template>
                </draggable>
            </div>

            <div class="mt-4 pt-3 border-t">
                <h4 class="mb-2 text-sm font-medium">Resultaten per pagina</h4>
                <Select v-model="preferences.perPage" @update:model-value="updatePreferences()">
                    <SelectTrigger class="w-[180px]">
                        <SelectValue placeholder="Selecteer" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectItem v-for="size in [15, 30, 50, 100]" :key="size" :value="size">
                                {{ size }}
                            </SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>
            </div>
        </DropdownMenuContent>
    </DropdownMenu>
</template>