import { useNow } from '@vueuse/core';
import { startOfDay } from 'date-fns';
import { computed } from 'vue';

export function useMinimumEntryDate() {
  const now = useNow();

  return computed(() => {
    return startOfDay(now.value);
  });
}