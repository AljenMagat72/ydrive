<script setup lang="ts">
import { Icon } from "@iconify/vue";
import { ref, watch, computed } from "vue";
import Button from "~/components/Button.vue";

// Props from parent
const props = defineProps<{
  visible: boolean;
  loading: boolean;
  isDisabled: boolean;
  initialStart: string;
  initialEnd: string;
  lastTimeShiftEnd: string;
  onStartChange?: () => void;
  onEndChange?: () => void;
}>();

const emit = defineEmits<{
  (e: "close"): void;
  (e: "submit"): void;
  (e: "update:start", value: string): void;
  (e: "update:end", value: string): void;
}>();

// Local state
const start = ref(props.initialStart);
const end = ref(props.initialEnd);

// Trigger parent checkers when inputs change
watch(start, (newVal, oldVal) => {
  if (!newVal || newVal === oldVal) return;

  emit("update:start", newVal);
  props.onStartChange?.();
});

watch(end, (newVal, oldVal) => {
  if (!newVal || newVal === oldVal) return;

  emit("update:end", newVal);
});

// Disable submit button if any value is missing or disabled
const canSubmit = computed(() => {
  return start.value && end.value && !props.isDisabled;
});

const handleSubmit = () => {
  if (!canSubmit.value) return;
  emit("submit");

  // Reset values
  start.value = "";
  end.value = "";
};

const isDisabledStart = (id: number) => {
  if (props.lastTimeShiftEnd === "00:00") return false;

  const endTimeId = getScheduleId(props.lastTimeShiftEnd);

  if (!endTimeId) return false;

  return id <= endTimeId.id;
};

const getScheduleId = (time: string) =>
  scheduleTimeSlots.find((e) => e.time == time);
</script>

<template>
  <div
    v-if="props.visible"
    class="mt-2 p-3 border rounded-xl shadow-md bg-white dark:bg-[#262728]"
  >
    <form @submit.prevent="handleSubmit">
      <div class="flex flex-col space-y-2 w-full">
        <div class="flex flex-col w-full relative">
          <span class="absolute ml-[80%]">
            <Icon icon="line-md:minus" class="text-white text-2xl" />
          </span>
          <label class="lg:text-sm text-xl color-blue dark:text-white/80">
            Start Time</label
          >
          <select
            id="fruit-select"
            v-model="start"
            class="border rounded-full lg:py-2 p-3 dark:text-white/80"
          >
            <option
              v-for="timeSlot in scheduleTimeSlots"
              :key="timeSlot.id"
              :value="timeSlot.time"
              :disabled="isDisabledStart(timeSlot.id)"
            >
              {{ timeSlot.label }}
            </option>
          </select>

          <label
            class="flex flex-col lg:text-sm text-xl color-blue mt-3 dark:text-white/80"
          >
            End Time
          </label>
          <select
            id="fruit-select"
            v-model="end"
            class="border rounded-full lg:py-2 p-3 dark:text-white/80"
          >
            <option
              v-for="timeSlot in scheduleTimeSlots"
              :key="timeSlot.id"
              :value="timeSlot.time"
            >
              {{ timeSlot.label }}
            </option>
          </select>
        </div>

        <div class="lg:mt-2 mt-3">
          <Button
            type="submit"
            :disabled="!canSubmit || props.loading"
            class="background-blue lg:py-3 py-6 lg:text-sm md:text-sm text-xl rounded-full w-full dark:text-white/80"
          >
            {{ props.loading ? "Saving..." : "Add Shift" }}
          </Button>
        </div>
      </div>
    </form>
  </div>
</template>

<style scoped>
/* Optional styling */
</style>
