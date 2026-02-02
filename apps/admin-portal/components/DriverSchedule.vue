<script setup lang="ts">
import { reactive, computed, watch, ref } from "vue";
import { fetchDeleteSlot } from "~/lib/api/drivers";
import AddScheduleForm from "./driver/schedule/AddScheduleForm.vue";
import { ArrowLeft, ArrowLeftIcon, Minus, Plus } from "lucide-vue-next";

const schedule = useSchedule();
const dayToAddSchedule = ref("");
const loading = ref(false);
const isDisabled = ref(false);

// Add new driver shift
const startShift = ref("");
const endShift = ref("");
const lastTimeEndShiftEnded = ref("");
const totalHours = ref("");

// Props from parent
const props = defineProps({
  driver: {
    type: Object,
    required: true,
  },
  weeklySchedule: {
    type: Object,
    required: true,
  },
});

// -------------------------------
// Helpers
// -------------------------------
const createEmptyWeek = () => {
  const today = new Date();
  //const formattedDate = today.toISOString().split("T")[0];
  const dayOfWeek = today.getDay(); // 0=Sun ... 6=Sat
  const monday = new Date(today);
  monday.setDate(today.getDate() - (dayOfWeek === 0 ? 6 : dayOfWeek - 1)); // get Monday

  const weekDays = [
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
    "Sunday",
  ];

  return weekDays.map((name, index) => {
    const date = new Date(monday);
    date.setDate(monday.getDate() + index);
    return {
      name,
      expanded: false,
      slots: [],
      date,
    };
  });
};

const week = reactive(createEmptyWeek());

// -------------------------------
// Watch driver changes
// -------------------------------
watch(
  () => props.driver,
  (newDriver) => {
    // Reset week
    week.splice(0, week.length, ...createEmptyWeek());

    if (!newDriver?.schedules) return;

    Object.entries(newDriver.schedules).forEach(
      ([dayKey, slots]: [string, unknown]) => {
        const dayName =
          dayKey.charAt(0).toUpperCase() + dayKey.slice(1).toLowerCase();
        const day: any = week.find((d) => d.name === dayName);
        if (!day) return;

        day.slots = Array.isArray(slots)
          ? slots.map((s) => {
              // API format: { id, "0": "time-range" }
              if (s["0"])
                return {
                  formatted: s["0"],
                  id: s.id || null,
                };

              // fallback
              return {
                formatted: "",
                id: s.id || null,
              };
            })
          : [];
      }
    );
  },
  {
    immediate: true,
  }
);

// Get total hours everytime schedule changes
watch(
  () => props.weeklySchedule,
  () => {
    totalHours.value = calculateWeeklyAndDailyHours(
      props.weeklySchedule
    ).weeklyTotal;
  },
  {
    immediate: true,
  }
);

// -------------------------------
// Week Range (Mon – Sun)
// -------------------------------
const dateRange = computed(() => {
  const today = new Date();
  const dayOfWeek = today.getDay();
  const monday = new Date(today);
  monday.setDate(today.getDate() - (dayOfWeek === 0 ? 6 : dayOfWeek - 1));

  const start = monday.toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
  });
  const sunday = new Date(monday);
  sunday.setDate(monday.getDate() + 6);
  const end = sunday.toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
  });

  return `${start} – ${end}`;
});

// -------------------------------
// Today Name
// -------------------------------
const todayName = computed(() => {
  const days = [
    "Sunday",
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
  ];
  return days[new Date().getDay()] ?? "Monday";
});

// Remove schedule from DB with 24-hour restriction
const deleteSplitSchedule = async (slotId: number, day: string) => {
  if (!slotId) return;

  // Find the schedule object in the weeklySchedule
  const slot = props.weeklySchedule[day]?.find((s: any) => s.id === slotId);
  if (!slot) return alert("Schedule not found.");

  // Confirm deletion
  const confirmed = window.confirm(
    "Are you sure you want to delete this schedule?"
  );
  if (!confirmed) return;

  try {
    // Call backend to delete
    await fetchDeleteSlot(slotId);

    // Remove schedule from UI
    props.weeklySchedule[day] = props.weeklySchedule[day].filter(
      (sched: any) => sched.id !== slotId
    );

    // Update hours
    totalHours.value = calculateWeeklyAndDailyHours(
      props.weeklySchedule
    ).weeklyTotal;
  } catch (error) {
    console.error("Failed to delete slot:", error);
    alert("Failed to delete schedule.");
  }
};

const dayOrder: Record<string, number> = {
  Monday: 1,
  Tuesday: 2,
  Wednesday: 3,
  Thursday: 4,
  Friday: 5,
  Saturday: 6,
  Sunday: 7,
};

const handleAddSchedule = (day: string, today: string) => {
  const dailySchedule = props.weeklySchedule[day];

  const dayIndex = dayOrder[day];
  const todayIndex = dayOrder[today];

  if (dayIndex === undefined || todayIndex === undefined) return;

  if (dayIndex < todayIndex) {
    alert(`Unable to add schedule on previous day.`);
  } else {
    dayToAddSchedule.value = day;
    lastTimeEndShiftEnded.value =
      getTheLastTimePreviousShiftEnded(dailySchedule);
  }
};

// Checker for new added start shift
const onStartChange = () => {
  isDisabled.value = false;
  const dailySchedule = props.weeklySchedule[dayToAddSchedule.value];
  const lastTimeOfShift = getTheLastTimePreviousShiftEnded(dailySchedule);

  if (dailySchedule?.length > 0) {
    // Checker for start shift
    if (startShift.value <= lastTimeOfShift) {
      startShift.value = "";
      return alert(
        "New start shift must be later than the driver's last end shift."
      );
    }

    // Checker for 30 minutes shift interval
    if (toMinutes(startShift.value) - toMinutes(lastTimeOfShift) < 30) {
      return alert(
        "Unable to add start shift, driver's shift interval atleast 30 minutes."
      );
    }
  }
};

// Submit new added drivers schedule
async function submitNewShift(dayDate: string) {
  if (!startShift.value) return alert("Please provide start shift.");

  if (!endShift.value) return alert("Please provide end shift.");

  let endDayDate = dayDate;

  if (endShift.value < startShift.value && endShift.value != "00:00") {
    const date = new Date(dayDate);
    date.setDate(date.getDate() + 1);

    endDayDate = date.toISOString().split("T")[0] ?? dayDate;
  }

  loading.value = true;

  try {
    const response: any = await schedule.addByAdmin(
      props.driver.id,
      `${dayDate} ${startShift.value}:00`,
      `${endDayDate} ${endShift.value}:00`
    );

    if (response.success) {
      if (endShift.value < startShift.value && endShift.value != "00:00") {
        // Insert first schedule
        const firstSchedule = {
          id: response.slot_id[0],
          time: formatTime(startShift.value, "00:00"),
        };
        props.weeklySchedule[dayToAddSchedule.value]?.push(firstSchedule);

        // Insert second schedule
        const indexOfDaysToAddSchedule = daysOfTheWeek.indexOf(
          dayToAddSchedule.value
        );
        const nextDay = daysOfTheWeek[indexOfDaysToAddSchedule + 1] ?? "";
        const scheduleContinuation = {
          id: response.slot_id[1],
          time: formatTime("00:00", endShift.value),
        };
        props.weeklySchedule[nextDay]?.push(scheduleContinuation);
      } else {
        // Add new schedule to ui
        const newAddSchedule = {
          id: response.slot_id[0],
          time: formatTime(startShift.value, endShift.value),
        };

        props.weeklySchedule[dayToAddSchedule.value]?.push(newAddSchedule);
      }

      // Reset form values
      startShift.value = "";
      endShift.value = "";
    } else {
      alert("Failed to add new shift.");
    }
  } catch (err: unknown) {
    if (err instanceof Error) {
      alert(err.message);
    } else {
      alert("An unexpected error occurred");
    }
  } finally {
    isDisabled.value = false;
    loading.value = false;
    dayToAddSchedule.value = "";

    totalHours.value = calculateWeeklyAndDailyHours(
      props.weeklySchedule
    ).weeklyTotal;
  }
}

const closeAddForm = () => (dayToAddSchedule.value = "");
</script>

<template>
  <div
    class="flex flex-col w-full h-full shadow-blue border rounded-xl p-3 dark:bg-[#262728]"
  >
    <div class="flex justify-between">
      <div class="flex flex-col items-start">
        <!-- Driver Name -->
        <h2
          class="font-semibold text-2xl md:text-base sm:text-lg text-center dark:text-white/80"
        >
          {{ driver?.firstName }} {{ driver?.lastName }}
        </h2>

        <!-- Date Range -->
        <p
          class="text-blue-600 font-semibold text-center text-sm md:text-xs sm:text-xs dark:text-white/60"
        >
          {{ dateRange }}
        </p>
        <p
          class="text-gray-500 dark:text-white/80 font-normal text-center text-sm md:text-xs sm:text-xs"
        >
          {{ totalHours }}
        </p>
      </div>
      <div class="">
        <button
          class="text-[#0078d4] dark:text-white/80 lg:text-sm text-xl mb-2 font-semibold md:hidden"
          @click="$emit('deselect')"
        >
          <ArrowLeft :size="32" />
        </button>
      </div>
    </div>

    <!-- Days List -->
    <div class="py-4 space-y-6 sm:space-y-4">
      <div v-for="day in week" class="" :key="day.name">
        <!-- Day Header -->
        <div
          class="flex justify-between rounded-full shadow-blue items-center px-2 py-1 cursor-pointer"
          :class="{
            'background-blue text-white': day.name === todayName,
            'color-blue': day.name !== todayName,
          }"
        >
          <!-- Day Name on the left -->
          <span
            class="text-xl md:text-xs sm:text-xs font-semibold dark:text-white/80"
            >{{ day.name }}</span
          >

          <!-- Date and toggle on the right -->
          <div class="flex items-center space-x-3 md:space-x-2 sm:space-x-1">
            <span
              class="text-xl text-gray-400 md:text-[10px] sm:text-[9px] dark:text-white/80"
              :class="{
                'background-blue text-white': day.name === todayName,
                'color-blue': day.name !== todayName,
              }"
            >
              {{
                day.date.toLocaleDateString("en-US", {
                  month: "short",
                  day: "numeric",
                })
              }}
            </span>
            <button
              class="text-2xl leading-none md:text-lg sm:text-base dark:text-white/80"
              @click="
                dayToAddSchedule === day.name
                  ? closeAddForm()
                  : handleAddSchedule(day.name, todayName)
              "
            >
              {{ dayToAddSchedule === day.name ? "x" : "+" }}
            </button>
          </div>
        </div>

        <div v-if="props.weeklySchedule[day?.name]?.length > 0">
          <div
            v-for="schedule in props.weeklySchedule[day.name]"
            class="flex justify-between rounded-full shadow-blue items-center px-2 py-1 cursor-pointer mt-[2px] dark:text-white/80"
            :class="{
              'background-blue text-white': day.name === todayName,
              'color-blue]': day.name !== todayName,
            }"
            :key="day.name"
          >
            <span class="lg:text-[12px] text-xl">{{
              formatTimeamToAmAndpmToPm(schedule.time)
            }}</span>
            <button
              class="text-xl leading-none md:text-xl sm:text-base"
              @click="deleteSplitSchedule(schedule.id, day.name)"
            >
              <Minus :size="18" />
            </button>
          </div>
        </div>

        <Transition
          enter-active-class="transition ease-out duration-500"
          enter-from-class="opacity-0 translate-y-2"
          enter-to-class="opacity-100 translate-y-0"
          leave-active-class="transition ease-in duration-500"
          leave-from-class="opacity-100 translate-y-0"
          leave-to-class="opacity-0 translate-y-2"
        >
          <AddScheduleForm
            :visible="dayToAddSchedule === day.name"
            :loading="loading"
            :isDisabled="isDisabled"
            :initialStart="startShift"
            :initialEnd="endShift"
            :onStartChange="onStartChange"
            :lastTimeShiftEnd="lastTimeEndShiftEnded"
            @close="closeAddForm"
            @submit="submitNewShift(formatDayDate(day.date))"
            @update:start="(val: any) => (startShift = val)"
            @update:end="(val: any) => (endShift = val)"
          />
        </Transition>
      </div>
    </div>
  </div>
</template>

<style scoped>
.icon-size {
  font-size: 2rem;
}
</style>
