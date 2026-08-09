import { useQuery } from '@tanstack/vue-query';
import axios from 'axios';
import { type DateValue } from 'reka-ui';
import { parseDateTime, type CalendarDateTime } from '@internationalized/date';
import { computed, type Ref, unref } from 'vue';


import DriverScheduleController from '@/wayfinder/actions/App/Http/Controllers/Driver/DriverScheduleController';

interface Shift {
  driverId: string;
  startsAt: string;
  endsAt: string;
}

type ShiftTime = {
  startsAt: CalendarDateTime,
  endsAt: CalendarDateTime,
}

export function useCityScheduleQuery(date: Ref<DateValue>) {
  return useQuery({
    queryKey: ['schedule', computed(() => date.value.toString())],
    queryFn: async function () {
      const { data } = await axios.get<Shift[]>(DriverScheduleController.city.url('me'), {
        params: { date: unref(date) }
      });
      return data;
    },
    select: function (data) {
      return data.reduce((acc, { driverId, startsAt, endsAt }) =>
        acc.set(driverId, [...(acc.get(driverId) || []), { startsAt: parseDateTime(startsAt), endsAt: parseDateTime(endsAt) }]),
        new Map<string, ShiftTime[]>()
      );
    }
  });
}
