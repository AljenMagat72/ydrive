<script setup lang="ts">
import type { CalendarDateTime, DateValue } from '@internationalized/date';
import { computed } from 'vue';
import {
  Tooltip,
  TooltipTrigger,
  TooltipProvider,
} from '@/components/ui/tooltip';

const props = defineProps<{
  date: DateValue,
  startsAt: CalendarDateTime,
  endsAt: CalendarDateTime,
}>();

const style = computed(() => {
  const startTimeStamp = props.startsAt.day !== props.date.day ? 0 : props.startsAt.hour * 60 + props.startsAt.minute;
  const endTimeStamp = props.endsAt.day !== props.date.day ? 1440 : props.endsAt.hour * 60 + props.endsAt.minute;

  const left = startTimeStamp / 1440;
  const width = (endTimeStamp - startTimeStamp) / 1440;

  return {
    width: `${width * 100}%`,
    left: `${left * 100}%`,
  }
});

</script>

<template>
  <div
    class="relative col-start-1 row-start-1"
    :style="style"
  >
    <TooltipProvider>
      <Tooltip>
        <TooltipTrigger
          ref="container"
          class="w-full bg-foreground text-background p-2 rounded-md text-xs block truncate select-none"
        >
          Driver
        </TooltipTrigger>
      </Tooltip>
    </TooltipProvider>
  </div>
</template>
