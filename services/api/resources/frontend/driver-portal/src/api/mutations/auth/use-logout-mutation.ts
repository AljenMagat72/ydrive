import DriverAuthController from '@/wayfinder/actions/App/Http/Controllers/Driver/DriverAuthController';
import { login } from '@/wayfinder/routes/driver';
import { router } from '@inertiajs/vue3';
import { useMutation } from '@tanstack/vue-query';
import axios from 'axios';

export function useLogoutMutation() {
  return useMutation({
    mutationKey: ['logout'],
    mutationFn: async () => {
      axios.post(DriverAuthController.logout().url);
      router.visit(login.url(), { replace: true });
    },
  })
}
