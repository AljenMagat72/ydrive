<script setup lang="ts">
import { computed } from 'vue';
import { type CalendarDate, type CalendarDateTime, parseDateTime } from '@internationalized/date';

import { useFetch, useRoute } from '#app';

import Chart from './Chart.vue';

const props = defineProps<{
  date: CalendarDate,
  city: string
}>();

const formattedDate = computed(() => props.date.toString());
const formattedCity = computed(() => props.city);

const route = useRoute();
const key = route.query.key || '';

const { data, status } = useFetch('/api/v1/admin/driver/schedule/daily', {
  key: 'daily-schedule',
  server: false,
  query: {
    date: formattedDate,
    city: formattedCity,
  },
  retry: false,
  headers: {
    'X-Admin-Key': key as string,
  }
});

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
      return !(value.endsAt.day === props.date.day && value.endsAt.hour === 0 && value.endsAt.minute === 0);
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
  <Chart
    :date="date"
    :city="city"
    :schedules="schedules"
    :loading="loading"
  />
</template>