<script setup lang="ts">
import { computed, ref, useTemplateRef, type Ref } from 'vue';
import { type DateValue } from 'reka-ui';
import { today, getLocalTimeZone } from '@internationalized/date';

import { useDragScroll } from '@/composables/use-drag-scroll.ts';

import { Card } from '@/components/ui/card';
import { timeSlots } from '@/consts/time-slots.ts';

import CityScheduleRow from './CityScheduleRow.vue';
import CityScheduleBackground from './CityScheduleBackground.vue';
import CityScheduleTimer from './CityScheduleTimer.vue';
import { useCityScheduleQuery } from '@/api/queries/use-city-schedule-query.ts';
import DatePicker from '../input/DatePicker.vue';

const container = useTemplateRef('container');

useDragScroll(container);

const date = ref(today(getLocalTimeZone())) as Ref<DateValue>;

const { data, isPending } = useCityScheduleQuery(date);

const visible = computed(() => {
  const now = today(getLocalTimeZone());
  return now.compare(date.value) === 0;
});
</script>

<template>
  <div class="flex flex-col h-full w-full">
    <div class="pb-2">
      <DatePicker v-model="date" />
    </div>
    <div class="flex-1 w-full relative">
      <Card class="absolute inset-0 py-0 overflow-hidden">
        <div
          ref="container"
          class="overflow-hidden cursor-all-scroll flex flex-col h-full"
        >
          <div class="grow text-sm whitespace-nowrap flex flex-col min-w-max">
            <div
              class="grid sticky top-0 z-10"
              style="grid-template-columns: repeat(24, minmax(0, 1fr));"
            >
              <div
                v-for="timeSlot in timeSlots"
                :key="timeSlot"
                class="bg-background text-center font-semibold p-2 border-b sticky top-0 z-10"
              >
                {{ timeSlot.slice(0, 5) }}
              </div>
            </div>
            <div class="relative grow h-full">
              <CityScheduleBackground />
              <div
                v-if="!isPending"
                class="relative mt-2 space-y-2"
              >
                <div
                  v-for="([key, value]) in data"
                  :key="key"
                  class="grid grid-cols-1 grid-rows-1"
                >
                  <CityScheduleRow
                    v-for="(schedule, index) in value"
                    :key="index"
                    :date="date"
                    :starts-at="schedule.startsAt"
                    :ends-at="schedule.endsAt"
                  />
                </div>
              </div>
              <CityScheduleTimer v-if="visible" />
            </div>
          </div>
        </div>
      </Card>
    </div>
  </div>
</template>
