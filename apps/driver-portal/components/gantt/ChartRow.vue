<script setup lang="ts">
import type { CalendarDate, CalendarDateTime } from '@internationalized/date';
import { computed } from 'vue';
import {
  Tooltip,
  TooltipTrigger,
  TooltipContent,
  TooltipProvider,
} from '~/components/ui/tooltip';

const props = defineProps<{
  date: CalendarDate,
  startsAt: CalendarDateTime,
  endsAt: CalendarDateTime,
  firstName: string,
  phoneNumber?: string,
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

function dateFormat(date: CalendarDateTime) {
  return `${date.hour.toString().padStart(2, '0')}:${date.minute.toString().padStart(2, '0')}`
}
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
          {{ firstName }}
        </TooltipTrigger>
        <TooltipContent :side-offset="-10">
          <div class="space-y-1">
            <p>
              Shift: {{ dateFormat(startsAt) }} - {{ dateFormat(endsAt) }}
            </p>
            <p>
              {{ firstName }}
              <a
                v-if="phoneNumber"
                class="underline"
                :href="`tel:+${props.phoneNumber}`"
              >
                - {{ props.phoneNumber }}
              </a>
            </p>
          </div>
        </TooltipContent>
      </Tooltip>
    </TooltipProvider>
  </div>
</template>