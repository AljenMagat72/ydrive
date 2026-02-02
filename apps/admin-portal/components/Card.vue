<script setup lang="ts">
import type { HTMLAttributes } from "vue";
import { cn } from "~/lib/utils";

const props = defineProps<{
  class?: HTMLAttributes["class"];
}>();
</script>

<template>
  <div
    data-slot="card"
    :class="
      cn(
        'lg:bg-card text-card-foreground flex flex-col rounded-xl lg:border lg:shadow-sm text-center',
        props.class
      )
    "
  >
    <slot />
  </div>
</template>
