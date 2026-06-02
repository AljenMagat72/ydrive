<script setup lang="ts">
import { ref } from 'vue';
import { type DateValue } from 'reka-ui';
import { Calendar } from '../ui/calendar';
import { Popover, PopoverContent, PopoverTrigger } from '../ui/popover';
import { Button } from '../ui/button/index.ts';
import { Calendar1 } from 'lucide-vue-next';

const date = defineModel<DateValue>({ required: true });
const isPopoverOpen = ref(false);

function handleDateSelect(value: DateValue | undefined) {
  if (value) {
    date.value = value;
    isPopoverOpen.value = false;
  }
}
</script>

<template>
  <Popover v-model:open="isPopoverOpen">
    <PopoverTrigger as-child>
      <Button variant="outline" class="justify-start text-left font-normal">
        <Calendar1 class="mr-2 h-4 w-4" />
        <span>{{ date ? date.toString() : 'Pick a date' }}</span>
      </Button>
    </PopoverTrigger>

    <PopoverContent class="w-auto p-0" align="start">
      <Calendar
        :model-value="date"
        @update:model-value="handleDateSelect"
        fixed-weeks
      />
    </PopoverContent>
  </Popover>
</template>
