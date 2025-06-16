<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'
import { inject } from 'vue'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()

const isInHeader = inject('isTableHeader', false)
</script>

<template>
  <tr
    data-slot="table-row"
    :class="cn('data-[state=selected]:bg-muted border-b transition-colors', isInHeader ? '' : 'hover:bg-muted/50 hover:cursor-pointer', props.class)"
  >
    <slot />
  </tr>
</template>
