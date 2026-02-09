<script setup lang="ts">
import type { CalendarDate, CalendarDateTime } from "@internationalized/date";
import { computed } from "vue";
import {
  Tooltip,
  TooltipTrigger,
  TooltipContent,
  TooltipProvider,
} from "~/components/ui/tooltip";

const props = defineProps<{
  date: CalendarDate;
  startsAt: CalendarDateTime;
  endsAt: CalendarDateTime;
  firstName: string;
  lastName: string;
  phoneNumber?: string;
}>();

function timeToMinutes(
  dt: CalendarDateTime,
  currentDay: CalendarDate,
  isStart = true,
) {
  if (dt.day < currentDay.day) return 0;

  if (dt.day > currentDay.day) return 1440;

  if (!isStart && dt.hour === 0 && dt.minute === 0) return 1440;

  if (isStart && dt.hour === 0 && dt.minute === 0) return 0;

  return dt.hour * 60 + dt.minute;
}

const style = computed(() => {
  const startTimeStamp = timeToMinutes(props.startsAt, props.date, true);
  const endTimeStamp = timeToMinutes(props.endsAt, props.date, false);

  const left = startTimeStamp / 1440;
  const width = (endTimeStamp - startTimeStamp) / 1440;

  return {
    width: `${width * 100}%`,
    left: `${left * 100}%`,
  };
});

function dateFormat(date: CalendarDateTime, isEnd = false) {
  if (isEnd && date.hour === 23 && date.minute === 59) {
    return "24:00";
  }
  return `${date.hour.toString().padStart(2, "0")}:${date.minute
    .toString()
    .padStart(2, "0")}`;
}
</script>

<template>
  <div class="relative col-start-1 row-start-1" :style="style">
    <TooltipProvider>
      <Tooltip>
        <TooltipTrigger
          ref="container"
          class="w-full bg-foreground text-background p-1 lg:p-2 rounded-md text-xs block select-none"
        >
          <span class="hidden lg:inline">{{ firstName }} {{ lastName }}</span>
          <span class="lg:hidden">{{ firstName.charAt(0) }}. {{ lastName }}</span>
        </TooltipTrigger>
        <TooltipContent :side-offset="-10">
          <div class="space-y-1">
            <p>
              Shift: {{ dateFormat(startsAt) }} - {{ dateFormat(endsAt, true) }}
            </p>
            <p>
              {{ firstName }} {{ lastName }}
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
