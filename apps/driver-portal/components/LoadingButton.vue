<script setup lang="ts">
import { Loader2 } from 'lucide-vue-next';
import Button from './ui/button/Button.vue';
import { cn } from '~/lib/utils';
import type { HTMLAttributes } from 'vue';

const emit = defineEmits(['click'])

const props = defineProps<{
  updating?: boolean,
  disabled?: boolean,
  type?: string,
  class?: HTMLAttributes['class'],
}>();
</script>

<template>
  <Button
    :class="cn('relative overflow-hidden', props.class)"
    :type="type"
    :disabled="updating || disabled"
    @click="emit('click')"
  >
    <div
      v-if="updating"
      class="size-fit m-auto absolute inset-0"
    >
      <Loader2 class="w-2 h-2 animate-spin" />
    </div>
    <span :class="{ 'invisible': updating }"><slot /></span>
  </Button>
</template>