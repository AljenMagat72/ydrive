import { useMutation } from '@tanstack/vue-query';
import axios from 'axios';
import { startOfWeek } from 'date-fns';
import { toDateString } from '@/lib/utils';
import { toast } from 'vue-sonner';
import DriverScheduleController from '@/wayfinder/actions/App/Http/Controllers/Driver/DriverScheduleController';

type RemoveShiftParams = {
  date: Date;
  id: string;
};

export function useRemoveShiftMutation() {
  return useMutation({
    mutationKey: ['remove-shift'],

    mutationFn: async ({ id }: RemoveShiftParams) => {
      await axios.delete(DriverScheduleController.delete({ driver: 'me', schedule: id }).url);
    },

    onMutate: async (vars, context) => {
      const weekStart = toDateString(
        startOfWeek(vars.date, { weekStartsOn: 1 })
      );
      const queryKey = ['schedule', weekStart];

      await context.client.cancelQueries({ queryKey });

      const previousShifts = context.client.getQueryData<any[]>(queryKey);

      context.client.setQueryData(queryKey, (old: any[] = []) =>
        old.filter(shift => shift.id !== vars.id)
      );

      return { previousShifts, queryKey };
    },

    onError: (_err, _vars, ctx, context) => {
      if (!ctx) return;

      context.client.setQueryData(ctx.queryKey, ctx.previousShifts);
      toast.error('Something went wrong');
    },

    onSuccess: (_data, _vars, ctx, context) => {
      if (!ctx) return;

      context.client.invalidateQueries({ queryKey: ctx.queryKey });
      toast.info('Shift removed');
    },
  });
}
