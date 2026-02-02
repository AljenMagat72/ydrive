<script setup>
import Sidebar from "~/components/Sidebar.vue";
import Header from "~/components/Header.vue";
</script>

<template>
  <div
    class="max-w-[1440px] mx-auto w-full flex flex-col min-h-screen bg-white dark:bg-[#1C1C1D]"
  >
    <Header />
    <div
      class="flex flex-1 flex-col md:flex-row gap-4 mr-3 h-screen dark:bg-[#1C1C1D]"
    >
      <Sidebar class="hidden md:flex w-[15%] flex-none" />
      <main class="w-full mt-6 px-3">
        <slot />
      </main>
    </div>
  </div>
</template>
