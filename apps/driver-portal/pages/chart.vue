<script setup lang='ts'>
import { ref, type Ref } from 'vue';
import type { CalendarDate } from '@internationalized/date';

import { definePageMeta } from '#imports';

import {
  Select,
  SelectTrigger,
  SelectValue,
  SelectGroup,
  SelectItem,
  SelectContent
} from '~/components/ui/select';

import PopoverCalendar from '~/components/input/PopoverCalendar.vue';
import ChartContainer from '~/components/gantt/ChartContainer.vue';
import { now } from '~/consts/days';

definePageMeta({
  layout: 'empty',
  middleware: ['chart-key']
});

const date = ref<CalendarDate>(now) as Ref<CalendarDate>;
const city = ref('Peterborough');
</script>

<template>
  <div class="flex flex-col h-dvh gap-y-4 p-4">
    <ClientOnly>
      <div class="flex gap-x-4">
        <PopoverCalendar v-model="date" />
        <Select
          v-model="city"
          default-value="Peterborough"
        >
          <SelectTrigger class="w-[140px]">
            <SelectValue />
          </SelectTrigger>
          <SelectContent>
            <SelectGroup>
              <SelectItem value="Peterborough">Peterborough</SelectItem>
              <SelectItem value="Huntsville">Huntsville</SelectItem>
              <SelectItem value="Lindsay">Lindsay</SelectItem>
              <SelectItem value="Port Hope / Cobourg">Port Hope / Cobourg</SelectItem>
              <SelectItem value="Grande Prairie">Grande Prairie</SelectItem>
              <SelectItem value="Medicine Hat">Medicine Hat</SelectItem>
              <SelectItem value="Lethbridge">Lethbridge</SelectItem>
            </SelectGroup>
          </SelectContent>
        </Select>
      </div>

      <ChartContainer
        :date="date"
        :city="city"
      />
    </ClientOnly>
  </div>
</template>
