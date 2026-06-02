<script setup lang="ts">
import { computed, type Ref, ref, useTemplateRef } from 'vue';
import PopoverRangeCalendar from '../PopoverRangeCalendar.vue';
import type { DateRange, DateValue } from 'reka-ui';
import { startOfWeek, endOfWeek, CalendarDate } from '@internationalized/date';
import { now } from '@/consts/days';
import { useDateFormat } from '@vueuse/core';
import { CalendarDays } from 'lucide-vue-next';

const popup = useTemplateRef('popup')

const range = defineModel<DateRange>({
  default: {
    start: startOfWeek(now, 'en-US', 'mon'),
    end: endOfWeek(now, 'en-US', 'mon')
  }
});

const visibleRange = ref<DateRange>({
  start: startOfWeek(now, 'en-US', 'mon'),
  end: endOfWeek(now, 'en-US', 'mon'),
}) as Ref<DateRange>;

const endOfNextWeek = endOfWeek(now.add({ weeks: 1 }), 'en-US', 'mon');

const parsedStartDate = computed(() => {
  if(!range.value.start) return new Date();

  return new Date(range.value.start.year, range.value.start.month-1, range.value.start.day);
});

const parsedEndDate = computed(() => {
  if(!range.value.end) return new Date();

  return new Date(range.value.end.year, range.value.end.month-1, range.value.end.day);
});

const startDateFormatted = useDateFormat(parsedStartDate, 'MMM Do');
const endDateFormatted = useDateFormat(parsedEndDate, 'MMM Do');

function onUpdateStart(e: DateValue | undefined) {
  if (!e) return;

  popup.value?.closePopover();

  const start = startOfWeek(new CalendarDate(e.year, e.month, e.day), 'en-US', 'mon');
  const end = endOfWeek(new CalendarDate(e.year, e.month, e.day), 'en-US', 'mon');

  range.value = {
    start,
    end
  };
}

function onOpenCalendar() {
  visibleRange.value = {
    start: range.value.start,
    end: range.value.end,
  }
}

</script>

<template>
  <PopoverRangeCalendar
    ref="popup"
    v-model="visibleRange"
    prevent-deselect
    :max-value="endOfNextWeek"
    @update:start-value="onUpdateStart"
    @open="onOpenCalendar"
  >
    <div class="flex flex-row gap-x-2 items-center">
      <CalendarDays /> {{ `${startDateFormatted} - ${endDateFormatted}` }}
    </div>
  </PopoverRangeCalendar>
</template>
