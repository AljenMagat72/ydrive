<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'
import { type ButtonVariants, buttonVariants } from '.'

interface Props {
  variant?: ButtonVariants['variant']
  size?: ButtonVariants['size']
  class?: HTMLAttributes['class'],
  disabled?: boolean,
}

const props = withDefaults(defineProps<Props>(), {
  as: 'button',
  disabled: false,
});

const emit = defineEmits(['click']);

</script>

<template>
  <button
    :disabled="disabled"
    :class="cn(buttonVariants({ variant, size }), props.class)"
    @click="emit('click')"
  >
    <slot />
  </button>
</template>
