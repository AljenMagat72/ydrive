<script setup lang="ts">
import { computed, useTemplateRef } from "vue";
import {
  type CalendarDate,
  type CalendarDateTime,
  today,
} from "@internationalized/date";

import { useDragScroll } from "#imports";

import { Card } from "~/components/ui/card";
import { timeSlots } from "~/consts/time-slots";

import ChartRow from "./ChartRow.vue";
import ChartBackground from "./ChartBackground.vue";
import ChartTimer from "./ChartTimer.vue";

type Driver = {
  id: number;
  firstName: string;
  lastName: string;
  phoneNumber?: string;
};

type ScheduleItem = {
  driver: Driver;
  startsAt: CalendarDateTime;
  endsAt: CalendarDateTime;
};

const props = defineProps<{
  date: CalendarDate;
  city: string;
  schedules: Map<number, Array<ScheduleItem>>;
  loading?: boolean;
}>();

const container = useTemplateRef("container");

useDragScroll(container);

const driverCount = computed(() => {
  let count = 0;
  props.schedules.forEach((scheduleItems) => {
    count += scheduleItems.length;
  });
  return count;
});

const timezone = computed(() => {
  switch (props.city) {
    case "Peterborough":
    case "Huntsville":
    case "Lindsay":
    case "Port Hope / Cobourg":
      return "America/Toronto";

    case "Grande Prairie":
    case "Medicine Hat":
    case "Lethbridge":
      return "America/Edmonton";

    default:
      return "America/Toronto";
  }
});

const visible = computed(() => {
  const now = today(timezone.value);
  return now.compare(props.date) === 0;
});
</script>

<template>
  <Card class="flex-grow py-0 lg:overflow-hidden overflow-scroll">
    <div
      ref="container"
      class="lg:overflow-hidden overflow-scroll cursor-all-scroll flex flex-col h-full"
    >
      <div
        class="flex-grow text-sm whitespace-nowrap flex flex-col min-w-fit min-h-fit"
      >
        <!-- Responsive Header -->
        <div
          class="grid auto-cols-max sticky top-0 z-10"
          style="grid-template-columns: 2rem repeat(24, minmax(1.5rem, 1fr))"
        >
          <!-- Row numbers column header -->
          <div class="bg-background text-center font-semibold p-1 lg:p-2 border-b sticky top-0 z-10 text-xs lg:text-sm">
            #
          </div>
          <div
            v-for="timeSlot in timeSlots"
            :key="timeSlot"
            class="bg-background text-center font-semibold p-1 lg:p-2 border-b sticky top-0 z-10 text-xs lg:text-sm"
          >
            <span class="hidden lg:inline">{{ timeSlot.slice(0, 5) }}</span>
            <span class="lg:hidden">{{ timeSlot.slice(0, 2) }}</span>
          </div>
        </div>
        
        <!-- Responsive Schedule Content -->
        <div class="relative flex-grow h-full">
          <ChartBackground />
          <div v-if="!loading" class="relative mt-2 space-y-1 lg:space-y-2">
            <div
              v-for="[key, value], scheduleIndex in schedules"
              :key="key"
              class="grid grid-cols-1 grid-rows-1"
              style="grid-template-columns: 2rem repeat(24, minmax(1.5rem, 1fr))"
            >
              <!-- Row number -->
              <div class="col-start-1 row-start-1 flex items-center justify-center text-gray-400 text-xs font-medium select-none border-r border-gray-200">
                {{ scheduleIndex + 1 }}
              </div>
              <ChartRow
                v-for="(schedule, index) in value"
                :key="index"
                :date="date"
                :first-name="schedule.driver.firstName"
                :last-name="schedule.driver.lastName"
                :starts-at="schedule.startsAt"
                :ends-at="schedule.endsAt"
                :phone-number="schedule.driver.phoneNumber"
                style="grid-column: 2 / 26"
              />
            </div>
          </div>
          <ChartTimer v-if="visible" :timezone="timezone" />
        </div>
      </div>
    </div>
  </Card>
</template>