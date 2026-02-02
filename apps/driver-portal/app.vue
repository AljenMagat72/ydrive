<template>
  <NuxtLoadingIndicator :throttle="0" />
  <NuxtLayout>
    <NuxtRouteAnnouncer />
    <NuxtPage />
  </NuxtLayout>
</template>

<style>
/* Slide Right Transition (Forward) */
.slide-up-enter-active,
.slide-up-leave-active {
  transition: opacity 0.3s ease-out;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.slide-up-enter-from {
  opacity: 0;
  transform: scale(0.95);
  filter: blur(8px);
}

.slide-up-enter-to {
  opacity: 1;
  transform: scale(1);
  filter: blur(0px);
}

.slide-up-leave-from {
  opacity: 1;
  transform: scale(1);
  filter: blur(0px);
}

.slide-up-leave-to {
  opacity: 0;
  transform: scale(1.05);
  /* Expand slightly */
  filter: blur(10px);
  /* Heavy blur */
}
</style>
