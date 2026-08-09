<script setup lang="ts">
import { Info } from 'lucide-vue-next';
import { computed, type Ref, ref } from 'vue';
import { type DateRange } from 'reka-ui';
import { startOfWeek, endOfWeek } from '@internationalized/date';
import { TooltipProvider, Tooltip, TooltipTrigger, TooltipContent } from './ui/tooltip';
import ScheduleDay from './ScheduleDay.vue';
import WeekSelect from './input/WeekSelect.vue';

import { now } from '@/consts/days';
import { useSchedule } from '@/hooks/use-schedule';
import { useDriverQuery } from '@/api/queries/use-driver-query';
import { Skeleton } from './ui/skeleton';

const range = ref({
  start: startOfWeek(now, 'en-US', 'mon'),
  end: endOfWeek(now, 'en-US', 'mon'),
}) as Ref<DateRange>;

const { schedule, isPending } = useSchedule(range);
const { data, isPending: isDriverQueryPending } = useDriverQuery();

const totalHours = computed(() => {
  let totalMs = 0;

  for (const day of schedule.value) {
    for (const shift of day.shifts) {
      totalMs += shift.end.getTime() - shift.start.getTime();
    }
  }

  return Math.round((totalMs / (1000 * 60 * 60)) * 100) / 100;
});
</script>

<template>
  <div class="space-y-2">
    <div class="flex flex-col-reverse sm:flex-row justify-between gap-y-2 items-start sm:items-center">
      <div class="w-full sm:w-auto">
        <WeekSelect class="w-full sm:w-auto" v-model="range" />
      </div>
      <div class="flex flex-col-reverse items-end sm:flex-row sm:items-center gap-x-2">
        <div class="flex flex-row items-center gap-x-2">
          <TooltipProvider>
            <Tooltip>
              <TooltipTrigger as-child>
                <div class="rounded-full">
                  <Info class="size-4" />
                </div>
              </TooltipTrigger>
              <TooltipContent>
                <p class="max-w-52">This is based on your submission when you signed up, should you need to change this
                  please email
                  <a
                    class="underline text-background"
                    href="mailto:mary@ydriveapp.com"
                  >mary@ydriveapp.com</a>
                </p>
              </TooltipContent>
            </Tooltip>
          </TooltipProvider>
          <p class="text-sm">
            Required:
            <Skeleton
              v-if="isDriverQueryPending"
              class="inline-block h-4 w-8 align-middle"
            />
            <span
              v-else
              class="bold"
            >{{ data?.minimumScheduledHours }} Hrs</span>
          </p>
          <p class="text-sm">
            Total:
            <Skeleton
              v-if="isPending"
              class="inline-block h-4 w-8 align-middle"
            />
            <span
              v-else
              class="bold"
            >{{ totalHours }} Hrs</span>
          </p>
        </div>
      </div>
    </div>
    <div class="flex flex-col gap-2">
      <ScheduleDay
        v-for="day in schedule"
        :key="day.date.toString()"
        :schedule="day"
        :is-pending="isPending"
        class="flex-1"
      />
    </div>
  </div>
</template>
