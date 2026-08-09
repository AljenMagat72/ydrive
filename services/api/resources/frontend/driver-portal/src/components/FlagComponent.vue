<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
  country?: string,
}>();

const flagEmoji = computed(() => {
  if (!props.country) return '';
  // Convert ISO country code to flag emoji
  return props.country
    .toUpperCase()
    .split('')
    .map(char => String.fromCodePoint(127397 + char.charCodeAt(0)))
    .join('');
});
</script>

<template>
  <span class="items-center justify-center rounded-sm text-lg">
    <span v-if="country">{{ flagEmoji }}</span>
  </span>
</template>