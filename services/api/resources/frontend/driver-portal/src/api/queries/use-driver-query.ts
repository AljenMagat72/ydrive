import DriverController from '@/wayfinder/actions/App/Http/Controllers/Driver/DriverController';
import { useQuery } from '@tanstack/vue-query';
import axios from 'axios';

export function useDriverQuery() {
  return useQuery({
    queryKey: ['user'],
    queryFn: async function() {
      const { data } = await axios.get(DriverController.get.url('me'));
      return data
    }
  });
}
