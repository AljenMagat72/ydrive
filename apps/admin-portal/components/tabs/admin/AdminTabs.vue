<script setup lang="ts">
const { currentSubPage, handleChangeSubpage } = defineProps<{
  currentSubPage: string;
  handleChangeSubpage?: (page: string) => void;
}>();
</script>

<template>
  <div
    class="flex justify-center place-items-center lg:mt-1 mt-6 lg:w-1/4 w-full mb-3"
  >
    <h4
      @click="handleChangeSubpage?.('admin-schedule')"
      class="w-full rounded-lg lg:py-1 py-3 px-3 cursor-pointer transition-all duration-500 ease-in-out flex place-items-center justify-center"
      :class="
        currentSubPage === 'admin-schedule'
          ? 'bg-[#0078D4] text-white scale-105 shadow-md'
          : 'hover:font-bold hover:bg-gray-100 dark:bg-[#262728] dark:text-white/70'
      "
    >
      Schedule
    </h4>
    <h4
      @click="handleChangeSubpage?.('admin-audit')"
      class="w-full rounded-lg lg:py-1 py-3 px-3 cursor-pointer transition-all duration-500 ease-in-out flex place-items-center justify-center"
      :class="
        currentSubPage === 'admin-audit'
          ? 'bg-[#0078D4] text-white scale-105 shadow-md'
          : 'hover:font-bold dark:bg-[#262728] hover:bg-gray-100 dark:text-white/80'
      "
    >
      Ops Audit
    </h4>
  </div>
</template>
