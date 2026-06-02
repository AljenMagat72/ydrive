<script setup lang="ts">
import { computed, watchEffect } from 'vue';
import { AcceptableValue } from 'reka-ui';

import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

interface TimeRange {
  start: number;
  end: number;
}

interface Props {
  increment?: number;
  placeholder?: string;
  min?: number;
  max?: number;
  disabledRanges?: TimeRange[];
}

const props = withDefaults(defineProps<Props>(), {
  increment: 30,
  placeholder: 'Select time',
  disabledRanges: () => []
});

const model = defineModel<number>();

function formatTimeLabel(minutes: number): string {
  const hours = Math.floor(minutes / 60) % 24;
  const mins = minutes % 60;
  const period = hours >= 12 ? 'PM' : 'AM';
  const displayHours = hours === 0 ? 12 : hours > 12 ? hours - 12 : hours;
  const displayMinutes = mins.toString().padStart(2, '0');
  return `${displayHours}:${displayMinutes} ${period}`;
}

function isTimeDisabled(minutes: number): boolean {
  if (props.min !== undefined && minutes <= props.min) {
    return true;
  }
  if (props.max !== undefined && minutes >= props.max) {
    return true;
  }

  return props.disabledRanges.some(range => {
    return minutes > range.start && minutes < range.end;
  });
}

const options = computed(() => {
  const options: Array<{ value: number; label: string; disabled: boolean }> = [];
  const totalMinutes = 24 * 60 + props.increment;

  for (let minutes = 0; minutes < totalMinutes; minutes += props.increment) {
    const label = formatTimeLabel(minutes);
    const disabled = isTimeDisabled(minutes);

    options.push({
      value: minutes,
      label,
      disabled,
    });
  }

  return options;
});

watchEffect(() => {
  if (model.value !== undefined && isTimeDisabled(model.value)) {
    model.value = undefined;
  }
});

function handleModelUpdate(value: AcceptableValue) {
  const numValue = Number(value);
  if (model.value === numValue) {
    model.value = undefined;
  } else {
    model.value = numValue;
  }
}
</script>

<template>
  <Select v-model="model" @update:model-value="handleModelUpdate">
    <SelectTrigger class="w-full">
      <SelectValue :placeholder="placeholder" />
    </SelectTrigger>
    <SelectContent :defaultValue="1440">
      <SelectGroup>
        <SelectItem
          v-for="option in options"
          :key="option.value"
          :value="option.value"
          :disabled="option.disabled"
        >
          {{ option.label }}
        </SelectItem>
      </SelectGroup>
    </SelectContent>
  </Select>
</template>