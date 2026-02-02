<script setup lang="ts">
import ScheduleRow from './schedule-row/ScheduleRow.vue';
import Table from './ui/table/Table.vue';
import TableHeader from './ui/table/TableHeader.vue';
import TableRow from './ui/table/TableRow.vue';
import TableHead from './ui/table/TableHead.vue';
import TableBody from './ui/table/TableBody.vue';
import TableCell from './ui/table/TableCell.vue';
import TableFooter from './ui/table/TableFooter.vue';
import LoadingButton from './LoadingButton.vue';
import { computed } from 'vue';
import type { CalendarDate, CalendarDateTime } from '@internationalized/date';

const props = defineProps<{
  startDay: CalendarDate,
  isLoading?: boolean;
  updating?: boolean;
  previousMin?: number;
  hideReplaceAll?: boolean;
}>();

const daysOfTheWeek = computed(() => {
  return Array.from({ length: 7 }, (_, i) => props.startDay.add({ days: i }));
});

const schedule = defineModel<Array<{
  startsAt?: CalendarDateTime,
  endsAt?: CalendarDateTime,
}>>({
  default: new Array(7).fill({
    startsAt: undefined,
    endsAt: undefined
  })
});

const overlapConstraints = computed(() => {
  return schedule.value.map((_, i) => {
    if (i === 0) return -Infinity;

    const prev = schedule.value[i - 1];

    return prev.endsAt && prev.startsAt && prev.endsAt.compare(prev.startsAt) === 1
      ? (prev.endsAt.hour * 60) + prev.endsAt.minute
      : -Infinity;
  });
});

const isValid = computed(() => {
  return schedule.value.every(item => {
    return (item.startsAt === undefined && item.endsAt === undefined) || (item.startsAt !== undefined && item.endsAt !== undefined);
  });
});

const emit = defineEmits(['submit']);

function onSubmit() {
  emit('submit');
}

function onApplyToAll(start: CalendarDateTime, end: CalendarDateTime) {
  for (const day of schedule.value) {
    day.startsAt = start;
    day.endsAt = end;
  }
}

</script>

<template>
  <div class="w-full border rounded-md bg-background">
    <Table class="table border-spacing-2">
      <TableHeader>
        <TableRow>
          <TableHead>Date</TableHead>
          <TableHead>Hours</TableHead>
          <TableHead />
        </TableRow>
      </TableHeader>
      <TableBody>
        <template
          v-for="(day, index) of 7"
          :key="day"
        >
          <ScheduleRow
            v-model="schedule[index]"
            :hide-replace-all="hideReplaceAll"
            :day=daysOfTheWeek[index]
            :is-loading="isLoading"
            :min-start="overlapConstraints[index]"
            @apply-to-all="onApplyToAll"
          />
        </template>
      </TableBody>
      <TableFooter>
        <TableRow>
          <TableCell />
          <TableCell />
          <TableCell>
            <LoadingButton
              class="relative overflow-hidden w-full"
              :updating="updating"
              :disabled="!isValid"
              @click="onSubmit"
            >
              Update
            </LoadingButton>
          </TableCell>
        </TableRow>
      </TableFooter>
    </Table>
  </div>
</template>