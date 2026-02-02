<script setup>
import { Edit, X } from "lucide-vue-next";

defineProps({
  show: Boolean,
});

defineEmits(["update:show"]);
</script>

<style scoped>
.fade-scale-enter-active,
.fade-scale-leave-active {
  transition: all 0.3s ease;
}
.fade-scale-enter-from,
.fade-scale-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
.fade-scale-enter-to,
.fade-scale-leave-from {
  opacity: 1;
  transform: scale(1);
}
</style>

<template>
  <transition
    name="fade-scale"
    enter-active-class="transition-all duration-500 ease-out"
    leave-active-class="transition-all duration-500 ease-in"
    enter-from-class="opacity-0 scale-95"
    enter-to-class="opacity-100 scale-100"
    leave-from-class="opacity-100 scale-100"
    leave-to-class="opacity-0 scale-95"
  >
    <div
      v-if="show"
      class="lg:hidden fixed inset-0 z-50 flex items-center justify-center bg-transparent backdrop-blur-xs px-10"
    >
      <div
        class="bg-white dark:bg-[#262728] dark:border dark:border-gray-500 rounded-xl shadow-lg max-w-lg w-full p-6 relative"
      >
        <button
          @click.stop="$emit('update:show', false)"
          class="absolute top-6 right-6 text-gray-500 hover:text-gray-700"
        >
          <X />
        </button>

        <div>
          <slot></slot>
        </div>
      </div>
    </div>
  </transition>
</template>
