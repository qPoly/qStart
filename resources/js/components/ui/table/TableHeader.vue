<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'
import { provide } from 'vue'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()

provide('isTableHeader', true)
</script>

<template>
  <thead
    data-slot="table-header"
    :class="cn('[&_tr]:border-b', props.class)"
  >
    <slot />
  </thead>
</template>
