<script setup lang="ts">
import { ref, useTemplateRef } from "vue";
import { CalendarIcon } from "lucide-vue-next";
import { onClickOutside } from "@vueuse/core";
import type { DateValue } from "reka-ui";

import Popover from "~/components/ui/popover/Popover.vue";
import PopoverTrigger from "~/components/ui/popover/PopoverTrigger.vue";
import PopoverContent from "~/components/ui/popover/PopoverContent.vue";
import Button from "~/components/ui/button/Button.vue";
import Calendar from "~/components/ui/calendar/Calendar.vue";
import type { CalendarDate } from "@internationalized/date";

defineOptions({
  inheritAttrs: false,
});

defineProps<{
  min?: DateValue;
  max?: DateValue;
}>();

const target = useTemplateRef<HTMLElement>("target");
onClickOutside(target, closePopover);

const isOpen = ref(false);

function openPopover() {
  isOpen.value = true;
}

function closePopover() {
  isOpen.value = false;
}

function onUpdateModel() {
  closePopover();
}

const model = defineModel<CalendarDate>();
</script>

<template>
  <Popover :open="isOpen" class="w-full">
    <PopoverTrigger as-child>
      <Button v-bind="$attrs" variant="outline" @click="openPopover">
        <span>{{ model ?? "Select a Date" }}</span>
        <CalendarIcon class="ms-auto h-4 w-4 opacity-50" />
      </Button>
    </PopoverTrigger>
    <PopoverContent class="lg:w-auto w-full p-0">
      <Calendar
        ref="target"
        v-model="model"
        prevent-deselect
        calendar-label="Select A Date"
        :min-value="min"
        :max-value="max"
        @update:model-value="onUpdateModel"
      />
    </PopoverContent>
  </Popover>
</template>
