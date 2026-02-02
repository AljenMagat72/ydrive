<script setup lang="ts">
import { definePageMeta, useAuth, useFetch } from "#imports";

import { computed, ref, type Ref } from "vue";
import {
  parseDateTime,
  type CalendarDate,
  type CalendarDateTime,
} from "@internationalized/date";
import { now } from "~/consts/days";

import PopoverCalendar from "~/components/input/PopoverCalendar.vue";
import Chart from "~/components/gantt/Chart.vue";

type Driver = {
  id: number;
  firstName: string;
  lastName: string;
  phoneNumber?: string;
};

type Schedule = {
  driver: Driver;
  startsAt: string;
  endsAt: string;
};

type ScheduleItem = {
  driver: Driver;
  startsAt: CalendarDateTime;
  endsAt: CalendarDateTime;
};

definePageMeta({
  layoutTransition: {
    name: "slide-up",
    mode: "out-in",
  },
  middleware: ["auth"],
});

const props = defineProps<{ drivers: Array<any> }>();

const date = ref<CalendarDate>(now) as Ref<CalendarDate>;
const selectedCity = ref("All");
const cities = ref([
  "Peterborough",
  "Sudbury",
  "Medicine Hat",
  "Cobourg",
  "Lindsay",
  "Lethbridge",
  "Huntsville",
  "Grande Prairie",
]);

const formattedDate = computed(() => date.value.toString());

const { data, status, refresh } = useFetch("/api/v1/driver/schedule/city", {
  key: "driver-daily-schedule",
  server: false,
  query: {
    date: formattedDate,
    city: selectedCity,
  },
  retry: false,
});

// Keep endsAt 00:00 on the same day by moving it to 23:59 for chart calculations
const schedules = computed(() => {
  if (!data.value) return new Map();

  return (data.value.schedules as Schedule[])
    .map((schedule) => {
      const startsAt = parseDateTime(schedule.startsAt);
      let endsAt = parseDateTime(schedule.endsAt);

      if (endsAt.hour === 0 && endsAt.minute === 0) {
        // Use the CalendarDateTime constructor / copy method
        endsAt = endsAt.add({ hours: 23, minutes: 59 });
      }

      return {
        driver: schedule.driver,
        startsAt,
        endsAt,
      };
    })
    .reduce((acc, value) => {
      if (!acc.has(value.driver.id)) {
        acc.set(value.driver.id, []);
      }
      acc.get(value.driver.id)!.push(value);
      return acc;
    }, new Map<number, ScheduleItem[]>());
});

const loading = computed(() => status.value === "pending");

watch([date, selectedCity], () => {
  refresh();
});
</script>

<template>
  <div class="flex flex-col flex-1 gap-y-4 lg:overflow-hidden overflow-scroll">
    <ClientOnly>
      <div class="flex gap-x-4 w-full">
        <PopoverCalendar v-model="date" />

        <select
          v-model="selectedCity"
          class="lg:w-auto w-full capitalize no-underline rounded-lg px-4 py-2 text-sm bg-white dark:bg-[#262728] border dark:text-white/50"
        >
          <option value="All" class="">All Cities</option>
          <option v-for="city in cities" :key="city" :value="city">
            {{ city.replace("_", " ") }}
          </option>
        </select>
      </div>
      <Chart
        :date="date"
        :city="selectedCity"
        :schedules="schedules"
        :loading="loading"
      />
    </ClientOnly>
  </div>
</template>
