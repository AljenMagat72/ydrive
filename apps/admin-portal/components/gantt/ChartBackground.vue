<script setup lang="ts">
const props = defineProps<{
  isAdmin?: boolean;
}>();
</script>

<template>
  <div class="absolute inset-0 h-full flex">
    <!-- Row numbers column background -->
    <div class="w-8 bg-background border-r border-gray-200" />
    <!-- Time slots background -->
    <div
      class="flex-1 bg-striped"
      style="grid-template-columns: repeat(24, minmax(1.5rem, 1fr));"
    />
  </div>
</template>

<style lang="css" scoped>
.bg-striped {
  background: repeating-linear-gradient(to right,
      var(--background) 0%,
      var(--background) 4.1666%,
      color-mix(in oklch, var(--background), gray 20%) 4.1666%,
      color-mix(in oklch, var(--background), gray 20%) 8.3333%);
}
</style>