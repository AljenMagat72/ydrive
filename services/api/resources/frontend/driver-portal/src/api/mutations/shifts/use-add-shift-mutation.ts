import { toDateString, toDateTimeString } from '@/lib/utils';
import DriverScheduleController from '@/wayfinder/actions/App/Http/Controllers/Driver/DriverScheduleController';
import { useMutation } from '@tanstack/vue-query';
import axios from 'axios';
import { startOfWeek } from 'date-fns';
import { toast } from 'vue-sonner';

type AddShiftParams = {
  date: Date;
  startsAt: number;
  endsAt: number;
};

export function useAddShiftMutation() {
  return useMutation({
    mutationKey: ['add-shift'],

    mutationFn: async ({ date, startsAt, endsAt }: AddShiftParams) => {
      const startsAtDate = new Date(date.getFullYear(), date.getMonth(), date.getDate(), 0, startsAt);
      const endsAtMinutes = startsAt >= endsAt ? endsAt + 1440 : endsAt;
      const endsAtDate = new Date(date.getFullYear(), date.getMonth(), date.getDate(), 0, endsAtMinutes);

      const { data } = await axios.post(DriverScheduleController.store('me').url, {
        startsAt: toDateTimeString(startsAtDate),
        endsAt: toDateTimeString(endsAtDate),
      });

      return data;
    },

    onMutate: async (vars, context) => {
      const weekStart = toDateString(startOfWeek(vars.date, { weekStartsOn: 1 }));
      const queryKey = ['schedule', weekStart];

      await context.client.cancelQueries({ queryKey });

      const previousShifts = context.client.getQueryData(queryKey);

      const startsAtDate = new Date(
        vars.date.getFullYear(),
        vars.date.getMonth(),
        vars.date.getDate(),
        0,
        vars.startsAt
      );

      const endsAtMinutes = vars.startsAt >= vars.endsAt ? vars.endsAt + 1440 : vars.endsAt;
      const endsAtDate = new Date(
        vars.date.getFullYear(),
        vars.date.getMonth(),
        vars.date.getDate(),
        0,
        endsAtMinutes
      );

      const optimisticShift = {
        id: `optimistic-${Date.now()}`,
        startsAt: toDateTimeString(startsAtDate),
        endsAt: toDateTimeString(endsAtDate),
        isOptimistic: true,
      };

      context.client.setQueryData(queryKey, (old: any[] = []) => [
        ...old,
        optimisticShift,
      ]);

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
      toast.success('Shift added');
    },
  });
}
