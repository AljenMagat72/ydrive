<script setup lang="ts">
import { now } from '@internationalized/date';
import { useTimestamp } from '@vueuse/core';
import { computed } from 'vue';

const timestamp = useTimestamp();

const props = defineProps<{
  timezone: string,
}>();

const todayDate = computed(() => {
  return now(props.timezone);
});

const startOfDay = computed(() => {
  return todayDate.value.set({ hour: 0, minute: 0, second: 0, millisecond: 0 });
});

const endOfDay = computed(() => {
  return startOfDay.value.add({ days: 1 });
});

const range = computed(() => {
  return {
    min: startOfDay.value.toDate().getTime(),
    max: endOfDay.value.toDate().getTime(),
  }
});

const position = computed(() => {
  const currentMs = timestamp.value;

  const totalDayMilliseconds = range.value.max - range.value.min;
  const elapsedMilliseconds = currentMs - range.value.min;
  const percentage = (elapsedMilliseconds / totalDayMilliseconds) * 100;

  return {
    left: `${Math.min(Math.max(percentage, 0), 100)}%`,
  };
});
</script>

<template>
  <ClientOnly>
    <div
      class="absolute top-0 w-1 h-full flex flex-col bg-foreground border-background border"
      :style="position"
    >
      <div class="flex-grow" />
    </div>
  </ClientOnly>
</template>