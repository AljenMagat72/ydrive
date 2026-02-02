<script setup lang="ts">
import { ref, useTemplateRef, type HTMLAttributes } from 'vue';
import { CalendarIcon } from 'lucide-vue-next';
import { onClickOutside, reactiveOmit } from '@vueuse/core';
import { useForwardPropsEmits, type RangeCalendarRootEmits, type RangeCalendarRootProps } from 'reka-ui'

import Popover from '~/components/ui/popover/Popover.vue'
import PopoverTrigger from '~/components/ui/popover/PopoverTrigger.vue';
import PopoverContent from '~/components/ui/popover/PopoverContent.vue';
import Button from '~/components/ui/button/Button.vue';
import RangeCalendar from '~/components/ui/range-calendar/RangeCalendar.vue';

const props = defineProps<RangeCalendarRootProps & { class?: HTMLAttributes['class'] }>();
const emits = defineEmits<RangeCalendarRootEmits>();

const delegatedProps = reactiveOmit(props, 'class')

const forwarded = useForwardPropsEmits(delegatedProps, emits)

const target = useTemplateRef<HTMLElement>('target')
onClickOutside(target, closePopover);

const isOpen = ref(false);

function openPopover() {
  isOpen.value = true;
}

function closePopover() {
  isOpen.value = false;
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
        variant="ghost"
        @click="openPopover"
      >
        <slot />
        <CalendarIcon class="ms-auto h-4 w-4" />
      </Button>
    </PopoverTrigger>
    <PopoverContent class="w-auto p-0">
      <RangeCalendar
        ref="target"
        v-bind="forwarded"
      />
    </PopoverContent>
  </Popover>
</template>