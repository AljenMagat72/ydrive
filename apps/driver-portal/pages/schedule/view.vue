<script setup lang='ts'>
import { definePageMeta, useAuth, useFetch } from '#imports';

import { computed, ref, type Ref } from 'vue';
import { parseDateTime, type CalendarDate, type CalendarDateTime } from '@internationalized/date';
import { now } from '~/consts/days';

import PopoverCalendar from '~/components/input/PopoverCalendar.vue';
import Chart from '~/components/gantt/Chart.vue';

type Driver = {
  id: number,
  firstName: string,
  lastName: string,
  phoneNumber?: string,
}

type Schedule = {
  driver: Driver,
  startsAt: string,
  endsAt: string,
}

type ScheduleItem = {
  driver: Driver;
  startsAt: CalendarDateTime;
  endsAt: CalendarDateTime;
};

definePageMeta({
  layoutTransition: {
    name: 'slide-up',
    mode: 'out-in'
  },
  middleware: ['auth']
});

const { user } = useAuth();

const date = ref<CalendarDate>(now) as Ref<CalendarDate>;
const formattedDate = computed(() => date.value.toString());

const { data, status } = useFetch('/api/v1/driver/schedule/city', {
  key: 'driver-daily-schedule',
  server: false,
  query: {
    date: formattedDate,
  },
  retry: false,
});

const schedules = computed(() => {
  if (data.value === null) return new Map();

  return (data.value.schedules as Schedule[])
    .map((schedule) => {
      return {
        driver: schedule.driver,
        startsAt: parseDateTime(schedule.startsAt),
        endsAt: parseDateTime(schedule.endsAt),
      }
    }).filter((value) => {
      return !(value.endsAt.day === date.value.day && value.endsAt.hour === 0 && value.endsAt.minute === 0);
    })
    .reduce((acc, value) => {
      if (!acc.has(value.driver.id)) {
        acc.set(value.driver.id, []);
      }

      acc.get(value.driver.id)?.push(value);
      return acc;
    }, new Map<number, Array<ScheduleItem>>());
});

const loading = computed(() => status.value === 'pending');
</script>

<template>
  <div class="flex flex-col flex-1 gap-y-4 overflow-scroll">
    <ClientOnly>
      <div class="flex gap-x-4">
        <PopoverCalendar v-model="date" />
      </div>

      <Chart
        v-if="user"
        :date="date"
        :city="user.cityId"
        :schedules="schedules"
        :loading="loading"
      />
    </ClientOnly>
  </div>
</template>