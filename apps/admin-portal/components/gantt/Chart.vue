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
        <div
          class="grid auto-cols-max sticky top-0 z-10"
          style="grid-template-columns: repeat(24, 1fr)"
        >
          <div
            v-for="timeSlot in timeSlots"
            :key="timeSlot"
            class="bg-background text-center font-semibold p-2 border-b sticky top-0 z-10"
          >
            {{ timeSlot.slice(0, 5) }}
          </div>
        </div>
        <div class="relative flex-grow h-full">
          <ChartBackground />
          <div v-if="!loading" class="relative mt-2 space-y-2">
            <div
              v-for="[key, value] in schedules"
              :key="key"
              class="grid grid-cols-1 grid-rows-1"
            >
              <ChartRow
                v-for="(schedule, index) in value"
                :key="index"
                :date="date"
                :first-name="schedule.driver.firstName"
                :last-name="schedule.driver.lastName"
                :starts-at="schedule.startsAt"
                :ends-at="schedule.endsAt"
                :phone-number="schedule.driver.phoneNumber"
              />
            </div>
          </div>
          <ChartTimer v-if="visible" :timezone="timezone" />
        </div>
      </div>
    </div>
  </Card>
</template>
