<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { useAppearance } from '@/composables/use-appearance';
import { router } from '@inertiajs/core';
import { Palette } from 'lucide-vue-next';
import { onUnmounted, ref } from 'vue';
import { Toaster } from 'vue-sonner';

defineProps<{
  title?: string;
  description?: string;
}>();

const { toggleAppearance, resolvedAppearance } = useAppearance();

const unsubscribe = router.on('navigate', (event) => {
  if (event.detail.page.url === '/verify') {
    transition.value = 'slide-right';
  } else {
    transition.value = 'slide-left';
  }
});

onUnmounted(unsubscribe);

const transition = ref('slide-left');
</script>

<template>
  <Toaster :theme="resolvedAppearance" />
  <div class="flex min-h-dvh max-w-dvw justify-center items-center px-2">
    <header class="absolute top-0 left-0 right-0 flex flex-row justify-end p-2">
      <Button
        @click="toggleAppearance"
        size="icon-xs"
        variant="ghost"
      >
        <Palette />
      </Button>
    </header>
    <Transition :name="transition">
      <slot />
    </Transition>
  </div>
</template>

<style scoped>
.slide-left-enter-active,
.slide-left-leave-active,
.slide-right-enter-active,
.slide-right-leave-active {
  transition-property: transform, opacity;
  transition-duration: 0.3s;
  transition-timing-function: ease-out;
}

.slide-left-enter-active,
.slide-right-enter-active {
  z-index: 10;
}

.slide-left-enter-from {
  transform: translateX(100%);
  opacity: 0;
}

.slide-left-enter-to {
  transform: translateX(0);
  opacity: 1;
}

.slide-left-leave-from {
  transform: translateX(0);
  opacity: 1;
}

.slide-left-leave-to {
  transform: translateX(-100%);
  opacity: 0;
}

.slide-right-enter-from {
  transform: translateX(-100%);
  opacity: 0;
}

.slide-right-enter-to {
  transform: translateX(0);
  opacity: 1;
}

.slide-right-leave-from {
  transform: translateX(0);
  opacity: 1;
}

.slide-right-leave-to {
  transform: translateX(100%);
  opacity: 0;
}

.slide-left-enter-active,
.slide-left-leave-active,
.slide-right-enter-active,
.slide-right-leave-active {
  position: absolute;
}
</style>
