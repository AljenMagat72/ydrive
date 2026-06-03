import DriverScheduleController from '@/wayfinder/actions/App/Http/Controllers/Driver/DriverScheduleController';
import { useQuery } from '@tanstack/vue-query';
import axios from 'axios';
import { type DateValue } from 'reka-ui';
import { computed, type Ref, unref } from 'vue';

export function useScheduleQuery(date: Ref<DateValue|undefined>) {
  return useQuery({
    queryKey: ['schedule', computed(() => date.value?.toString())],
    queryFn: async function () {
      const dateValue = unref(date);
      const { data } = await axios.get(DriverScheduleController.weekly.url('me'), { params: { date: dateValue } });
      return data;
    },
    enabled: computed(() => !!date.value)
  })
}
