<script setup lang="ts">
const isOpen = defineModel({
  default: false,
})
</script>

<template>
  <button
    @click="isOpen = !isOpen"
    class="relative w-6 h-6 focus:outline-none cursor-pointer"
    :aria-expanded="isOpen"
    aria-controls="main-menu"
    aria-label="Toggle navigation menu"
  >
    <span :class="[
      'absolute h-0.5 left-0 w-full bg-foreground transition-transform duration-300 ease-in-out',
      isOpen ? 'rotate-45 top-2.5' : 'top-1.5'
    ]" />
    <span :class="[
      'absolute h-0.5 left-0 w-full bg-foreground transition-opacity duration-300 ease-in-out',
      isOpen ? 'opacity-0' : 'top-3'
    ]" />
    <span :class="[
      'absolute h-0.5 left-0 w-full bg-foreground transition-transform duration-300 ease-in-out',
      isOpen ? '-rotate-45 top-2.5' : 'top-4.5'
    ]" />
  </button>
</template>
