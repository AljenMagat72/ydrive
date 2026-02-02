<script setup lang="ts">
import { formatTimeToAddHoursAndMinutes } from "#imports";
import { Bold, Clock } from "lucide-vue-next";

const props = defineProps({
  dailyTotal: {
    type: String,
    required: true,
  },
  visible: {
    type: Boolean,
    required: true,
  },
});
</script>

<template>
  <button
    v-if="visible"
    class="flex place-items-center mt-3 bg-gray-300 text-gray-700 rounded-full px-2 py-1 w-full justify-between font-semibold dark:text-gray-300 dark:bg-gray-700"
  >
    <span class="text-xs text-center"> {{ props.dailyTotal }}</span>
    <Clock class="text-blue-600" size="16" />
  </button>
</template>
