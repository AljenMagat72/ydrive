<script setup lang="ts">
import { computed } from 'vue';
import { Plus } from 'lucide-vue-next';
import { useDateFormat, useNow } from '@vueuse/core';
import { Button } from './ui/button';
import { Dialog, DialogContent, DialogTrigger } from './ui/dialog';
import { Card, CardContent } from './ui/card';

import ShiftEntry from './ShiftEntry.vue';

import { Skeleton } from './ui/skeleton';
import AddShiftDialog from './dialogs/AddShiftDialog.vue';
import { startOfDay } from 'date-fns';

const props = defineProps<{
  schedule: any,
  isPending: boolean,
}>();

const now = useNow();

const maximumDate = computed(() => {
  return startOfDay(now.value);
});

const dateFormat = useDateFormat(props.schedule.date, 'ddd, MMM Do');

const totalHours = computed(() => {
  let totalMs = 0;

  if (!props.schedule.shifts) return 0;

  for (const shift of props.schedule.shifts) {
    totalMs += shift.end.getTime() - shift.start.getTime();
  }

  return Math.round((totalMs / (1000 * 60 * 60)) * 100) / 100;
});
</script>

<template>
  <Card>
    <CardContent class="space-y-1">
      <div class="flex flex-row justify-between items-center gap-x-2">
        <div class="flex flex-row gap-x-2 text-sm">
          <p class="font-semibold">{{ dateFormat }}</p>
          <p>{{ totalHours }} Hrs</p>
        </div>
        <Dialog v-if="maximumDate <= schedule.date">
          <DialogTrigger as-child>
            <Button
              size="icon-xs"
              variant="ghost"
            >
              <Plus />
            </Button>
          </DialogTrigger>
          <DialogContent>
            <AddShiftDialog :date="schedule.date" />
          </DialogContent>
        </Dialog>
      </div>

      <Skeleton
        v-if="isPending"
        class="h-4 w-full"
      />

      <template v-else>
        <ShiftEntry
          v-for="shift in schedule.shifts"
          :key="shift.id"
          :shift="shift"
        />
        <p
          v-if="!schedule.shifts?.length"
          class="text-sm text-muted-foreground"
        >
          No shifts
        </p>
      </template>
    </CardContent>
  </Card>
</template>
