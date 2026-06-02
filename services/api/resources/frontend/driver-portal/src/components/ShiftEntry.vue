<script setup lang="ts">
import { computed } from 'vue';
import { useDateFormat } from '@vueuse/core';
import { Clock, Loader2, X } from 'lucide-vue-next';
import { Button } from './ui/button';
import { Dialog, DialogTrigger, DialogContent, DialogFooter, DialogClose, DialogTitle } from './ui/dialog';
import DialogDescription from './ui/dialog/DialogDescription.vue';

const props = defineProps<{
  shift: any;
}>();

const formattedStartTime = useDateFormat(props.shift.start, 'hh:mma');
const formattedEndTime = useDateFormat(props.shift.end, 'hh:mma');

const duration = computed(() => {
  return Math.round(((props.shift.end.getTime() - props.shift.start.getTime()) / (1000 * 60 * 60)) * 100) / 100;
});
</script>

<template>
  <div class="flex flex-row justify-between gap-x-2">
    <div class="text-sm flex flex-row items-center gap-2">
      <Clock class="size-4" />
      <p>{{ formattedStartTime }} to {{ formattedEndTime }}</p>
      <p class="text-muted-foreground">{{ duration }} Hrs</p>
    </div>
    <Dialog>
      <DialogTrigger as-child>
        <Button
          variant="ghost"
          size="icon-xs"
        >
          <Loader2
            v-if="shift.isOptimistic"
            class="animate-spin"
          />
          <X v-else />
        </Button>
      </DialogTrigger>
      <DialogContent>
        <DialogTitle>
          Remove Shift
        </DialogTitle>
        <DialogDescription>
          To remove a shift, please contact the support team
        </DialogDescription>
        <DialogFooter class="flex flex-row justify-between">
          <DialogClose as-child>
            <Button variant="secondary">Close</Button>
          </DialogClose>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>
