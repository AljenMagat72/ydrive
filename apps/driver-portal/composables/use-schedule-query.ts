import { useAsyncData } from "#app";
import { useSchedule } from "#imports";
//import { type CalendarDateTime, parseDateTime } from "@internationalized/date";
import type { DateRange } from "reka-ui";
import { computed, type Ref } from "vue";
//import { daysOfTheWeek } from "~/consts/days";

export function useScheduleQuery(range: Ref<{ start: any, end: any }>) {
  const { weekly } = useSchedule();

  return useAsyncData('weekly-schedule', () => {
    
    const startDate = range.value.start.toString(); 
    return weekly(startDate);
  }, {
    watch: [range]
  });
}
