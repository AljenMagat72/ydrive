import { computed, type Ref, toRef } from 'vue';
import { type DateRange } from 'reka-ui';

import { useScheduleQuery } from '@/api/queries/use-schedule-query';

export function useSchedule(range: Ref<DateRange>) {
  const { data, isPending } = useScheduleQuery(toRef(() => range.value.start));

  const schedule = computed(() => {
    if (!range.value?.start || !range.value?.end) {
      return [];
    }

    const total = range.value.end.compare(range.value.start) + 1;
    const start = new Date(range.value.start.year, range.value.start.month - 1, range.value.start.day);

    const days: any[] = [];

    for (let i = 0; i < total; i++) {
      days.push({
        date: new Date(start.getFullYear(), start.getMonth(), start.getDate() + i),
        shifts: [],
      });
    }

    if (!data.value) {
      return days;
    }

    for (const shift of data.value) {
      const startsAt = new Date(shift.startsAt);
      const endsAt = new Date(shift.endsAt);

      const index = Math.floor((startsAt.getTime() - start.getTime()) / (1000 * 60 * 60 * 24));

      if (index >= 0 && index < total) {
        days[index].shifts.push({
          start: startsAt,
          end: endsAt,
          id: shift.id,
          isOptimistic: !!shift.isOptimistic,
        });
      }
    }

    for (const day of days) {
      day.shifts.sort((a:any, b:any) => a.start.getTime() - b.start.getTime());
    }

    return days;
  });

  return { schedule, isPending }
}
