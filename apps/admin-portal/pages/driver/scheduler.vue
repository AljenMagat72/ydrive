<script setup lang="ts">
import { Download } from "lucide-vue-next";
import { definePageMeta, useAuth, useFetch } from "#imports";
import * as XLSX from 'xlsx';

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
  cityId: string;
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
const startTime = ref("");
const endTime = ref("");
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

// Generate time options for 30-minute intervals in 24-hour format
const timeOptions = computed(() => {
  const options = [];
  for (let hour = 0; hour < 24; hour++) {
    for (let minute = 0; minute < 60; minute += 30) {
      const timeString = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
      options.push(timeString);
    }
  }
  return options;
});

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


const schedules = computed(() => {
  if (!data.value) return new Map();

  return (data.value.schedules as Schedule[])
    .map((schedule) => {
      const startsAt = parseDateTime(schedule.startsAt);
      let endsAt = parseDateTime(schedule.endsAt);

      if (endsAt.hour === 0 && endsAt.minute === 0) {
        endsAt = endsAt.add({ hours: 23, minutes: 59 });
      }

      return {
        driver: schedule.driver,
        startsAt,
        endsAt,
      };
    })
    .filter((schedule) => {
      const hasValidCityId = schedule.driver.cityId && schedule.driver.cityId !== null && schedule.driver.cityId !== '';
      if (!hasValidCityId) return false;
      
      if (startTime.value) {
        const timeParts = startTime.value.split(':').map(Number);
        const startHour = timeParts[0] || 0;
        const startMinute = timeParts[1] || 0;
        if (schedule.startsAt.hour < startHour || 
            (schedule.startsAt.hour === startHour && schedule.startsAt.minute < startMinute)) {
          return false;
        }
      }
      
      if (endTime.value) {
        const timeParts = endTime.value.split(':').map(Number);
        const endHour = timeParts[0] || 0;
        const endMinute = timeParts[1] || 0;
        if (schedule.endsAt.hour > endHour || 
            (schedule.endsAt.hour === endHour && schedule.endsAt.minute > endMinute)) {
          return false;
        }
      }
      
      return true;
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

function exportDailySchedule() {
  const headers = [
    'Driver ID',
    'First Name',
    'Last Name',
    'City',
    'Start Time',
    'End Time',
    'Date'
  ];

  const rows: string[][] = [];
  schedules.value.forEach((scheduleItems) => {
    scheduleItems.forEach((schedule: any) => {
      rows.push([
        schedule.driver.id,
        schedule.driver.firstName || '',
        schedule.driver.lastName || '',
        schedule.driver.cityId || 'N/A',
        `${schedule.startsAt.hour.toString().padStart(2, '0')}:${schedule.startsAt.minute.toString().padStart(2, '0')}`,
        `${schedule.endsAt.hour.toString().padStart(2, '0')}:${schedule.endsAt.minute.toString().padStart(2, '0')}`,
        `${date.value.year}-${date.value.month.toString().padStart(2, '0')}-${date.value.day.toString().padStart(2, '0')}`
      ]);
    });
  });

  const wsData = [headers, ...rows];

  const wb = XLSX.utils.book_new();
  const ws = XLSX.utils.aoa_to_sheet(wsData);
  XLSX.utils.book_append_sheet(wb, ws, "Schedule");

  const excelBuffer = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });

  const blob = new Blob([excelBuffer], { 
    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' 
  });
  const link = document.createElement('a');
  const url = URL.createObjectURL(blob);

  link.setAttribute('href', url);
  link.setAttribute('download', `schedule-${date.value.year}-${date.value.month.toString().padStart(2, '0')}-${date.value.day.toString().padStart(2, '0')}.xlsx`);
  link.style.visibility = 'hidden';

  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}
  
watch([date, selectedCity, startTime, endTime], () => {
  refresh();
});
</script>

<template>
  <div class="flex flex-col flex-1 gap-y-4 lg:overflow-hidden overflow-scroll">
    <ClientOnly>
      <div class="flex flex-col lg:flex-row lg:justify-between gap-4 w-full">
        <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
          <PopoverCalendar v-model="date" />

          <select
            v-model="selectedCity"
            class="w-full sm:w-auto capitalize no-underline rounded-lg px-4 py-2 text-sm bg-white dark:bg-[#262728] border dark:text-white/50"
          >
            <option value="All" class="">All Cities</option>
            <option v-for="city in cities" :key="city" :value="city">
              {{ city.replace("_", " ") }}
            </option>
          </select>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-4 w-full lg:w-auto">
          <div class="flex items-center gap-2 w-full sm:w-auto">
            <select
              v-model="startTime"
              class="w-full sm:w-auto px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-[#262728] text-gray-900 dark:text-white"
            >
              <option value="">Start Time</option>
              <option v-for="time in timeOptions" :key="time" :value="time">
                {{ time }}
              </option>
            </select>
            <span class="text-gray-500 dark:text-gray-400 whitespace-nowrap">to</span>
            <select
              v-model="endTime"
              class="w-full sm:w-auto px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-[#262728] text-gray-900 dark:text-white"
            >
              <option value="">End Time</option>
              <option v-for="time in timeOptions" :key="time" :value="time">
                {{ time }}
              </option>
            </select>
          </div>

          <button
            @click="exportDailySchedule"
            class="flex items-center justify-center gap-2 px-4 py-2 text-sm bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors w-full sm:w-auto"
            title="Download Daily Schedule"
          >
            <Download class="w-4 h-4" /> Export
          </button>
        </div>
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
