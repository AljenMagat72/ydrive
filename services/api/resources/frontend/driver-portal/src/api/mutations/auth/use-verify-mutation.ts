import DriverAuthController from '@/wayfinder/actions/App/Http/Controllers/Driver/DriverAuthController';
import { useMutation } from '@tanstack/vue-query'
import axios from 'axios'

type VerifyResponse = {
  token: string;
}

export function useVerifyMutation() {
  return useMutation({
    mutationFn: async (payload: any) => {
      const { data } = await axios.post<VerifyResponse>(DriverAuthController.verify.url(), payload)
      return data
    },
  })
}
