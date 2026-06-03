<script setup lang="ts">
import { ref, useTemplateRef, type HTMLAttributes } from 'vue';
import { onClickOutside, reactiveOmit } from '@vueuse/core';
import { useForwardPropsEmits, type RangeCalendarRootEmits, type RangeCalendarRootProps } from 'reka-ui'

import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover';
import Button from '@/components/ui/button/Button.vue';
import RangeCalendar from '@/components/ui/range-calendar/RangeCalendar.vue';

const props = defineProps<RangeCalendarRootProps & { class?: HTMLAttributes['class'] }>();
const emits = defineEmits<RangeCalendarRootEmits & {
  open: [];
  close: [];
}>();

const delegatedProps = reactiveOmit(props, 'class')

const forwarded = useForwardPropsEmits(delegatedProps, emits)

const target = useTemplateRef<HTMLElement>('target')
onClickOutside(target, closePopover);

const isOpen = ref(false);

function openPopover() {
  isOpen.value = true;
  emits('open');
}

function closePopover() {
  isOpen.value = false;
  emits('close');
}

defineExpose({
  openPopover,
  closePopover,
});

</script>

<template>
  <Popover :open=isOpen>
    <PopoverTrigger as-child>
      <Button
        variant="outline"
        @click="openPopover"
        :class="class"
      >
        <slot />
      </Button>
    </PopoverTrigger>
    <PopoverContent
      class="w-auto p-0"
      :align="'start'"
    >
      <RangeCalendar
        ref="target"
        v-bind="forwarded"
        :week-starts-on="1"
      />
    </PopoverContent>
  </Popover>
</template>
