<script setup lang="ts">
import { definePageMeta, navigateTo, useRecaptcha } from "#imports";
import { onMounted, ref, watch } from "vue";
import LoginForm from "~/components/form/LoginForm.vue";
import PINCodeForm from "~/components/form/PINCodeForm.vue";

definePageMeta({
  layout: "empty",
  layoutTransition: {
    name: "slide-up",
    mode: "out-in",
    onEnter: (_, done) => {
      done();
    },
  },
});

const state = ref(0);
const transition = ref("slide-left");

watch(state, (prev, curr) => {
  transition.value = prev < curr ? "slide-right" : "slide-left";
});

function showApp() {
  navigateTo("/", { replace: true });
}

onMounted(() => {
  useRecaptcha().load();
});
</script>

<template>
  <div class="flex justify-center">
    <Transition :name="transition">
      <div
        v-if="state === 0"
        class="min-h-dvh flex flex-col items-center justify-center w-fit"
      >
        <LoginForm @next="state++" />
      </div>
      <div
        v-else
        class="min-h-dvh flex flex-col items-center justify-center w-fit"
      >
        <PINCodeForm @prev="state--" @next="showApp" />
      </div>
    </Transition>
  </div>
</template>

<style scoped>
/* Slide Left Transition (Forward) */
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
