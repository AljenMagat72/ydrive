import DriverSettingsController from '@/wayfinder/actions/App/Http/Controllers/Driver/DriverSettingsController';
import { useQuery } from '@tanstack/vue-query';
import axios from 'axios';

export function useSettingsQuery(key: string) {
  return useQuery({
    queryKey: ['setting', key],
    queryFn: async function() {
      const { data } = await axios.get(DriverSettingsController.show.url(key));
      return data
    }
  });
}
