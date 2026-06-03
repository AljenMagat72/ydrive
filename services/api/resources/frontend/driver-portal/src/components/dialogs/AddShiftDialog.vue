<script setup lang="ts">
import { useAddShiftMutation } from '@/api/mutations/shifts/use-add-shift-mutation';
import { useDateFormat } from '@vueuse/core';
import { ref, computed } from 'vue';

import { DialogFooter, DialogClose, DialogDescription, DialogTitle } from '@/components/ui/dialog';
import { Field, FieldGroup, FieldLabel } from '@/components/ui/field'
import HourInput from '../input/HourInput.vue';
import { Button } from '../ui/button';

const props = defineProps<{
  date: Date,
}>();

const dateFormat = useDateFormat(props.date, 'dddd MMMM Do');

const { mutateAsync: addShift } = useAddShiftMutation();

const start = ref<number>();
const end = ref<number>();

const disabledEndRanges = computed(() => {
  if (!start.value) return [];

  return [{
    start: start.value,
    end: start.value + 60 * 3
  }]
});

const shiftLength = computed(() => {
  if (start.value === undefined || end.value === undefined) return null;

  let minutes = end.value - start.value;

  if (minutes <= 0) {
    minutes += 1440;
  }

  const hours = minutes/60;

  return `${hours} Hr`;
});

function onSubmit() {
  if (start.value === undefined || end.value === undefined) return;

  addShift({ date: props.date, startsAt: start.value, endsAt: end.value });
}
</script>

<template>
  <form
    class="contents"
    @submit.prevent="onSubmit"
  >
    <DialogTitle>New Shift</DialogTitle>
    <DialogDescription>Add a new shift on {{ dateFormat }}</DialogDescription>
    <FieldGroup class="flex flex-row">
      <Field>
        <FieldLabel>Start Time:</FieldLabel>
        <HourInput v-model="start" />
      </Field>
      <Field>
        <FieldLabel>End Time:</FieldLabel>
        <HourInput
          v-model="end"
          :disabled-ranges="disabledEndRanges"
        />
      </Field>
    </FieldGroup>
    <p class="text-sm text-foreground">
      Total Duration: <span
        v-if="shiftLength"
        class="font-medium"
      >{{ shiftLength }}</span>
    </p>
    <DialogFooter>
      <DialogClose as-child>
        <Button
          type="submit"
          :disabled="start === undefined || end === undefined"
        >
          Submit
        </Button>
      </DialogClose>
    </DialogFooter>
  </form>
</template>
