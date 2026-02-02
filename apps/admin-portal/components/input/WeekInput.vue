<script setup lang="ts">
import { computed, nextTick, useTemplateRef } from 'vue';
import PopoverRangeCalendar from './PopoverRangeCalendar.vue';
import type { DateRange, DateValue } from 'reka-ui';
import { getLocalTimeZone, startOfWeek, endOfWeek, CalendarDate, DateFormatter } from '@internationalized/date';
import { now } from '~/consts/days';

const formatter = new DateFormatter('en-US', {
  month: 'long',
  day: 'numeric'
});

const popup = useTemplateRef('popup')

const range = defineModel<DateRange>({
  default: {
    start: startOfWeek(now, 'en-US'),
    end: endOfWeek(now, 'en-US')
  }
});

const endOfNextWeek = endOfWeek(now, 'en-US');

const formattedDateRange = computed(() => {
  if (!range.value.start || !range.value.end) return;

  return `${formatter.format(range.value.start.toDate(getLocalTimeZone()))} - ${formatter.format(range.value.end.toDate(getLocalTimeZone()))}`
});

async function onUpdateStart(e: DateValue | undefined) {
  if (!e) return;

  popup.value?.closePopover();

  /**
   * kind of a hack, we have to set the start value, await a tick
   * and then set the end.
   * end must be set to undefined for this to work
   */
  range.value = {
    start: startOfWeek(new CalendarDate(e.year, e.month, e.day), 'en-US'),
    end: undefined,
  };

  await nextTick();

  range.value = {
    start: startOfWeek(new CalendarDate(e.year, e.month, e.day), 'en-US'),
    end: endOfWeek(new CalendarDate(e.year, e.month, e.day), 'en-US')
  };
}

</script>

<template>
  <PopoverRangeCalendar
    ref="popup"
    v-model="range"
    prevent-deselect
    :max-value="endOfNextWeek"
    @update:start-value="onUpdateStart"
  >
    {{ formattedDateRange }}
  </PopoverRangeCalendar>
</template>