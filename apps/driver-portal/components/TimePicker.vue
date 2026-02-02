<script setup lang="ts">
import Select from './ui/select/Select.vue';
import SelectContent from './ui/select/SelectContent.vue';
import SelectGroup from './ui/select/SelectGroup.vue';
import SelectItem from './ui/select/SelectItem.vue';
import SelectTrigger from './ui/select/SelectTrigger.vue';
import SelectValue from './ui/select/SelectValue.vue';
import { timeIncrements } from '~/consts/time-slots';

const MIN_GAP = 180;

const props = defineProps<{
  placeholder?: string,
  disabled?: boolean,
  min?: number,
  max?: number,
  minValue?: number,
  maxValue?: number,
  gapDirection?: 'before' | 'after',
}>();

const model = defineModel<number>()

function isTimeDisabled(value: number): boolean {
  if(props.min && value <= props.min) return true;
  if(props.max && value >= props.max) return true;

  if (props.gapDirection === 'before' && props.maxValue !== undefined) {
    const diff = ((props.maxValue - value) % 1440 + 1440) % 1440;
    return diff < MIN_GAP;
  }
  
  if (props.gapDirection === 'after' && props.minValue !== undefined) {
    const diff = ((value - props.minValue) % 1440 + 1440) % 1440;
    return diff < MIN_GAP;
  }

  return false;
}
</script>

<template>
  <Select
    v-model="model"
    :disabled="disabled"
  >
    <SelectTrigger class="w-full">
      <SelectValue :placeholder="placeholder" />
    </SelectTrigger>
    <SelectContent>
      <SelectGroup>
        <SelectItem
          v-for="entry in timeIncrements"
          :key="entry.value"
          :value="entry.value"
          :disabled="isTimeDisabled(entry.value)"
        >
          {{ entry.formatted }}
        </SelectItem>
      </SelectGroup>
    </SelectContent>
  </Select>
</template>