<script setup lang="ts">
import { ReplaceAll, Trash2 } from 'lucide-vue-next';
import { computed, ref, watch, watchEffect } from 'vue';
import TableRow from '../ui/table/TableRow.vue';
import TableCell from '../ui/table/TableCell.vue';
import Button from '../ui/button/Button.vue';
import TimePicker from '../TimePicker.vue';
import Skeleton from '../ui/skeleton/Skeleton.vue';
import { type CalendarDate, toCalendarDateTime, type CalendarDateTime, getLocalTimeZone } from '@internationalized/date';
import { calendarDateFormatter, now } from '~/consts/days';

const emit = defineEmits(['apply-to-all']);

const props = defineProps<{
  day: CalendarDate;
  isLoading?: boolean;
  minStart: number;
  hideReplaceAll?: boolean;
}>();

const model = defineModel<{
  startsAt?: CalendarDateTime;
  endsAt?: CalendarDateTime;
}>({
  default: {
    startsAt: undefined,
    endsAt: undefined
  }
});

const startsAtHours = ref<number>();
const endsAtHours = ref<number>();

watchEffect(() => {
  if (model.value?.startsAt) {
    startsAtHours.value = model.value.startsAt.hour * 60 + model.value.startsAt.minute
  }
  if (model.value?.endsAt) {
    endsAtHours.value = model.value.endsAt.hour * 60 + model.value.endsAt.minute;
  }
});

watch([startsAtHours, endsAtHours], () => {
  const start = toCalendarDateTime(props.day);

  if (startsAtHours.value === undefined && endsAtHours.value === undefined) {
    model.value = {
      startsAt: undefined,
      endsAt: undefined,
    };
  } else if (startsAtHours.value !== undefined && endsAtHours.value === undefined) {
    model.value = {
      startsAt: start.add({ minutes: startsAtHours.value }),
      endsAt: undefined,
    };
  } else if (startsAtHours.value === undefined && endsAtHours.value !== undefined) {
    model.value = {
      startsAt: undefined,
      endsAt: start.add({ minutes: endsAtHours.value }),
    };
  } else if (startsAtHours.value !== undefined && endsAtHours.value !== undefined) {
    const duration = ((endsAtHours.value - startsAtHours.value) % 1440 + 1440) % 1440;

    model.value = {
      startsAt: start.add({ minutes: startsAtHours.value }),
      endsAt: start.add({ minutes: startsAtHours.value + duration }),
    };
  }
});

const editable = computed(() => {
  return props.day.compare(now) >= 0
});

function onClear() {
  startsAtHours.value = undefined;
  endsAtHours.value = undefined;
}

function onReplaceAll() {
  emit('apply-to-all', model.value.startsAt, model.value.endsAt);
}

function checkStartDiasbled(value: number) {
  if (endsAtHours.value === undefined) return false;

  const diff = ((endsAtHours.value - value) % 1440 + 1440) % 1440;
  return diff < 180;
}

function checkEndDisabled(value: number) {
  if (startsAtHours.value === undefined) return false;

  const diff = ((value - startsAtHours.value) % 1440 + 1440) % 1440;
  return diff < 180;
}

const clearDisabled = computed(() => props.isLoading || (!model.value.startsAt && !model.value.endsAt));
</script>

<template>
  <TableRow>
    <TableCell>{{ calendarDateFormatter.format(day.toDate(getLocalTimeZone())) }}</TableCell>
    <TableCell class="w-full">
      <div class="relative">
        <div
          class="flex flex-col md:flex-row gap-2 items-center justify-center transition-opacity"
          :class="{ 'opacity-0 pointer-events-none': isLoading }"
        >
          <TimePicker
            v-model="startsAtHours"
            :disabled="!editable"
            :disabled-check="checkStartDiasbled"
            :min="minStart"
            :max-value="endsAtHours"
            gap-direction="before"
            placeholder="Start Time"
          />
          <span>to</span>
          <TimePicker
            v-model="endsAtHours"
            :disabled="!editable"
            :min-value="startsAtHours"
            gap-direction="after"
            :disabled-check="checkEndDisabled"
            placeholder="End Time"
          />
        </div>
        <Transition name="fade">
          <div
            v-if="isLoading"
            class="absolute inset-0"
          >
            <Skeleton class="w-full h-full" />
          </div>
        </Transition>
      </div>
    </TableCell>
    <TableCell class="text-right">
      <div class="flex sm:flex-row flex-col gap-2">
        <Button
          :class="[{ 'invisible': !editable || hideReplaceAll }]"
          @click="onReplaceAll"
        >
          <ReplaceAll />
        </Button>
        <Button
          :class="[{ 'invisible': !editable }]"
          variant="destructive"
          :disabled="clearDisabled"
          @click="onClear"
        >
          <Trash2 />
        </Button>
      </div>
    </TableCell>
  </TableRow>
</template>

<style lang="css">
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>