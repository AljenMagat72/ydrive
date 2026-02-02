<script setup lang="ts">
import { definePageMeta, onBeforeRouteLeave, useSchedule } from "#imports";
import {
  startOfWeek,
  endOfWeek,
  type CalendarDate,
} from "@internationalized/date";
import { useEventListener } from "@vueuse/core";
import { ref, onMounted, type Ref } from "vue";
import NextSchedule from "~/components/NextSchedule.vue";
import { now } from "~/consts/days";

definePageMeta({
  layoutTransition: {
    name: "slide-up",
    mode: "out-in",
  },
  middleware: ["auth"],
});

// 1. Get nextWeekly from your composable
const { nextWeekly } = useSchedule();

const start = now;
const nextWeekStart = start.add({ days: 7 });

const range = ref<{
  start: CalendarDate;
  end: CalendarDate;
}>({
  start: startOfWeek(nextWeekStart, "en-US"),
  end: endOfWeek(nextWeekStart, "en-US"),
}) as Ref<{
  start: CalendarDate;
  end: CalendarDate;
}>;

const hasChanges = ref<boolean>(false);
const weeklySchedule = ref<any>(null); // Use any or your WeeklyScheduleResponse type

// 2. Fetch data specifically for next week using your new function
onMounted(async () => {
  try {
    const data = await nextWeekly();
    if (data) {
      weeklySchedule.value = data;
    }
  } catch (error) {
    console.error("Error loading next week schedule:", error);
  }
});

useEventListener(window, "beforeunload", (e) => {
  if (hasChanges.value) {
    e.preventDefault();
    return "";
  }
});
</script>

<template>
  <div class="">
    <NextSchedule
      v-if="weeklySchedule && Object.keys(weeklySchedule).length"
      :weekly-schedule="weeklySchedule"
    />
    <div v-else class="flex justify-center p-10">
       <p class="text-gray-500">Loading next week's schedule...</p>
    </div>
  </div>
</template>