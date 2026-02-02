<!-- eslint-disable vue/attribute-hyphenation -->
<script setup lang="ts">
import { definePageMeta, onBeforeRouteLeave, useScheduleQuery } from "#imports";
import {
  startOfWeek,
  endOfWeek,
  type CalendarDate,
} from "@internationalized/date";
import { useEventListener } from "@vueuse/core";
import { ref, watch, watchEffect, type Ref } from "vue";
import DriverSchedule from "~/components/DriverSchedule.vue";
import { now } from "~/consts/days";

definePageMeta({
  layoutTransition: {
    name: "slide-up",
    mode: "out-in",
  },
  middleware: ["auth"],
});

const start = now;

const range = ref<{
  start: CalendarDate;
  end: CalendarDate;
}>({
  start: startOfWeek(start, "en-US"),
  end: endOfWeek(start, "en-US"),
}) as Ref<{
  start: CalendarDate;
  end: CalendarDate;
}>;

const hasChanges = ref<boolean>(false);
const isDirty = ref<boolean>(false);
const weeklySchedule = ref<object>({});

const { data } = useScheduleQuery(range);

watch(
  data,
  () => {
    if (isDirty.value) {
      hasChanges.value = true;
    }

    isDirty.value = true;

    //console.log("API Result:", data.value);
    weeklySchedule.value = data.value;
  },
  { deep: true, flush: "post" }
);

watchEffect(() => {
  if (data.value) {
    weeklySchedule.value = data.value;
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
    <DriverSchedule
      v-if="weeklySchedule && Object.keys(weeklySchedule).length"
      :weekly-schedule="weeklySchedule"
    />
  </div>
</template>
