<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue';
import { Label } from './ui/label';
import { Image, Trash2 } from 'lucide-vue-next';

interface Props {
    name: string;
}

const props = defineProps<Props>();

const model = defineModel<File | string>({ required: true });
const dropEvents: Array<'dragenter' | 'dragover' | 'dragleave' | 'drop'> = ['dragenter', 'dragover', 'dragleave', 'drop'];

onMounted(() => {
    dropEvents.forEach((eventName) => {
        document.body.addEventListener(eventName, preventDefaults)
    });
});

onUnmounted(() => {
    dropEvents.forEach((eventName) => {
        document.body.removeEventListener(eventName, preventDefaults)
    });
});

const preventDefaults = (e: Event): void => {
    e.preventDefault();
}

const loadImage = (): string => {
    if (!(model.value instanceof File)) {
        return '/storage/' + model.value;
    }

    const preview = document.querySelectorAll<HTMLImageElement>('.preview');
    const blobUrl = URL.createObjectURL(model.value);

    preview.forEach(elem => {
        elem.onload = () => {
            URL.revokeObjectURL(elem.src);
        };
    });

    return blobUrl;
}

const addImage = (e: Event): void => {
    const target = e.target as HTMLInputElement;
    if (!target?.files) return;

    for (let file of Array.from(target.files)) {
        if (file.type.startsWith('image/')) {
            model.value = file;
        }
    }
}

const onDrop = (e: DragEvent): void => {
    if (!e.dataTransfer?.files) return;

    for (let file of Array.from(e.dataTransfer.files)) {
        if (file.type.startsWith('image/')) {
            model.value = file;
        }
    }
}

const removeImage = (): void => {
    model.value = '';
}
</script>
<template>
    <div class="space-y-2">
        <Label for="file-upload">Logo</Label>
        <label for="file-upload" class="mt-1 flex flex-col justify-center w-40 aspect-square border border-input rounded-md shadow-xs text-sm text-center text-gray-600" :class="{ 'cursor-pointer': !model }" @drop.prevent="onDrop">
            <template v-if="model">
                <div class="relative pt-[100%]">
                    <button class="absolute top-1 right-1 z-50 px-1 bg-white rounded-bl cursor-pointer" type="button" @click="removeImage()">
                        <Trash2 class="size-4" />
                    </button>
                    <img class="absolute inset-0 z-0 object-contain w-full h-full preview" :src="loadImage()" />
                </div>
            </template>

            <template v-if="!model">
                <Image class="mx-auto text-gray-300" />
                <div class="font-semibold text-1el-green">
                    <span>Selecteer bestand</span>
                    <input id="file-upload" :name="props.name" type="file" accept="image/*" class="sr-only" v-on:change="addImage($event)" />
                </div>
                <div> of drag & drop</div>
            </template>
        </label>
    </div>

</template>