import DriverAuthController from '@/wayfinder/actions/App/Http/Controllers/Driver/DriverAuthController';
import { useMutation } from '@tanstack/vue-query'
import axios from 'axios'

type LoginResponse = {}

export function useLoginMutation() {
  return useMutation({
    mutationFn: async (payload: any) => {
      const { data } = await axios.post<LoginResponse>(DriverAuthController.login.url(), payload)
      return data
    },
  })
}
