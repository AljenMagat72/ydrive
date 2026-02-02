<script setup lang="ts">
import { ref, watch, computed } from "vue";
import Button from "../ui/button/Button.vue";
import { scheduleTimeSlots } from "#imports";

// Props
const props = defineProps<{
  visible: boolean;
  loading: boolean;
  isDisabled: boolean;
  initialStart: string;
  initialEnd: string;
  lastTimeShiftEnd: string;
  scheduleDay: Date;
  bookedSlots?: { start: string; end: string }[];
  onStartChange?: () => void;
  onEndChange?: () => void;
}>();

const emit = defineEmits<{
  (e: "close"): void;
  (e: "submit"): void;
  (e: "update:start", value: string): void;
  (e: "update:end", value: string): void;
}>();

const localBookedSlots = ref(props.bookedSlots ? [...props.bookedSlots] : []);

watch(
  () => props.bookedSlots,
  (newSlots) => {
    localBookedSlots.value = newSlots ? [...newSlots] : [];
  },
  { deep: true }
);

// Local state
const start = ref(props.initialStart);
const end = ref(props.initialEnd);

// Watch for updates
watch(start, (val, oldVal) => {
  if (val && val !== oldVal) {
    emit("update:start", val);
    props.onStartChange?.();
  }
});

watch(end, (val, oldVal) => {
  if (val && val !== oldVal) {
    emit("update:end", val);
    props.onEndChange?.();
  }
});

const canSubmit = computed(() => !!start.value && !!end.value && !props.isDisabled);

// past time checker
const isPastTimeToday = (time: string) => {
  if (!props.scheduleDay) return false;

  const today = new Date();
  const isToday =
    today.getFullYear() === props.scheduleDay.getFullYear() &&
    today.getMonth() === props.scheduleDay.getMonth() &&
    today.getDate() === props.scheduleDay.getDate();

  if (!isToday) return false;

  const [h, m] = time.split(":").map(Number);
  const slot = new Date(props.scheduleDay);
  slot.setHours(h, m, 0, 0);

  return slot < today;
};

function parseSlotTime(time: string) {
  const match = time.match(/(\d{1,2}):(\d{2})(am|pm)/i);
  if (!match) return { h: 0, m: 0 };
  let [, hourStr, minStr, meridian] = match;
  let h = parseInt(hourStr);
  const m = parseInt(minStr);
  if (meridian.toLowerCase() === "pm" && h < 12) h += 12;
  if (meridian.toLowerCase() === "am" && h === 12) h = 0;
  return { h, m };
}

// disabled start times
const disabledStartTimes = computed(() => {
  if (!props.scheduleDay || !localBookedSlots.value) return [];

  const today = new Date();
  const isToday =
    today.getFullYear() === props.scheduleDay.getFullYear() &&
    today.getMonth() === props.scheduleDay.getMonth() &&
    today.getDate() === props.scheduleDay.getDate();

  const disabled = new Set<string>();

  let lastEnd: { h: number; m: number } | null = null;

  localBookedSlots.value.forEach((shift) => {
    const startStr = shift.start;
    const endStr = shift.end;

    if (!startStr || !endStr) return;

    const parseTime = (timeStr: string) => {
      const match = timeStr.match(/(\d{1,2}):(\d{2})(am|pm)/i);
      if (!match) return { h: 0, m: 0 };
      let [, hourStr, minStr, meridian] = match;
      let h = parseInt(hourStr);
      const m = parseInt(minStr);
      if (meridian.toLowerCase() === "pm" && h < 12) h += 12;
      if (meridian.toLowerCase() === "am" && h === 12) h = 0;
      return { h, m };
    };

    const start = parseTime(startStr);
    const end = parseTime(endStr);

    const shiftStart = new Date(props.scheduleDay);
    shiftStart.setHours(start.h, start.m, 0, 0);

    let shiftEnd = new Date(props.scheduleDay);
    shiftEnd.setHours(end.h, end.m, 0, 0);
    if (shiftEnd <= shiftStart) shiftEnd.setDate(shiftEnd.getDate() + 1); // overnight

    // update lastEnd for 30-min interval
    if (!lastEnd || shiftEnd > new Date(props.scheduleDay.setHours(lastEnd.h, lastEnd.m, 0, 0))) {
      lastEnd = { h: shiftEnd.getHours(), m: shiftEnd.getMinutes() };
    }

    // Disable slots inside shift
    scheduleTimeSlots.forEach((slot) => {
      const [h, m] = slot.time.split(":").map(Number);
      let slotDate = new Date(props.scheduleDay);
      slotDate.setHours(h, m, 0, 0);

      if (slotDate < shiftStart && shiftEnd > new Date(props.scheduleDay)) {
        slotDate.setDate(slotDate.getDate() + 1);
      }

      if (slotDate >= shiftStart && slotDate < shiftEnd) {
        disabled.add(slot.time);
      }
    });
  });

  // 30mins interval
  if (lastEnd) {
    const buffer = new Date(props.scheduleDay);
    buffer.setHours(lastEnd.h, lastEnd.m + 30, 0, 0);

    scheduleTimeSlots.forEach((slot) => {
      const [h, m] = slot.time.split(":").map(Number);
      const slotDate = new Date(props.scheduleDay);
      slotDate.setHours(h, m, 0, 0);
      if (slotDate < buffer) disabled.add(slot.time);
    });
  }

  // Disable past times today
  scheduleTimeSlots.forEach((slot) => {
    const [h, m] = slot.time.split(":").map(Number);
    const slotDate = new Date(props.scheduleDay);
    slotDate.setHours(h, m, 0, 0);
    if (isToday && slotDate < today) disabled.add(slot.time);
  });

  return Array.from(disabled);
});

// Submit handler
const handleSubmit = () => {
  if (!start.value || !end.value) return;

  //Prevent same start and end time
  if (start.value === end.value) {
    alert("Start time and end time cannot be the same.");
    return;
  }

  if (isPastTimeToday(start.value)) {
    alert("You cannot add a schedule in the past.");
    return;
  }

  emit("submit");

  // Reset after submit
  start.value = "";
  end.value = "";
};
</script>

<template>
  <div
    v-if="props.visible"
    class="mt-2 p-3 border rounded-xl shadow-md bg-white dark:bg-gray-900"
  >
    <form @submit.prevent="handleSubmit">
      <div class="flex flex-col space-y-2 w-full">
        <div class="grid grid-cols-1 lg:place-items-center">

          <!-- Start Time -->
          <div class="w-full">
            <label class="flex flex-col text-xs color-blue text-center">Start Time</label>
            <select v-model="start" class="border rounded-full px-3 py-1 w-full">
              <option
                v-for="slot in scheduleTimeSlots"
                :key="slot.id"
                :value="slot.time"
                :disabled="disabledStartTimes.includes(slot.time)"
                :class="disabledStartTimes.includes(slot.time) ? 'text-gray-400' : 'text-black'"
              >
                {{ slot.label }}
              </option>
            </select>
          </div>

          <!-- End Time -->
          <div class="w-full mt-2">
            <label class="flex flex-col text-xs color-blue lg:mt-1 mt-5 text-center">End Time</label>
            <select v-model="end" class="border rounded-full px-3 py-1 w-full">
              <option
                v-for="slot in scheduleTimeSlots"
                :key="slot.id"
                :value="slot.time"
                class="text-black"
              >
                {{ slot.label }}
              </option>
            </select>
          </div>

        </div>

        <div class="mt-1">
          <Button
            type="submit"
            :disabled="!canSubmit || props.loading"
            class="bg-[#0078d4] dark:bg-gray-700 dark:text-white rounded-full w-full"
          >
            {{ props.loading ? "Saving..." : "Add Shift" }}
          </Button>
        </div>
      </div>
    </form>
  </div>
</template>

<style scoped></style>
