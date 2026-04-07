<!-- eslint-disable vue/valid-v-for -->
<!-- eslint-disable vue/valid-v-for -->
<!-- eslint-disable vue/require-explicit-emits -->
<!-- eslint-disable vue/no-mutating-props -->
<!-- eslint-disable @typescript-eslint/no-explicit-any -->
<!-- eslint-disable vue/attribute-hyphenation -->
<script setup lang="ts">
import {
  addDurations,
  calculateWeeklyAndDailyHours,
  daysOfTheWeek,
  differenceInEndAndStartOfNewlyAddedShift,
  formatDayDate,
  formatTimeToAddHoursAndMinutes,
  getDuration,
  getTheLastTimePreviousShiftEnded,
  useSchedule,
} from "#imports";
import { reactive, computed, ref, watchEffect } from "vue";
import AddScheduleForm from "./form/AddScheduleForm.vue";
import { Info, Minus, ChevronDown } from "lucide-vue-next";
import DailyTotal from "./weekly/DailyTotal.vue";
import { useRouter } from "vue-router";
import { useAuth } from '#imports';
const { fetchDeleteSlot, addSchedule } = useSchedule();

//const toast = useToast();
const dayToAddSchedule = ref("");
const loading = ref(false);
const isDisabled = ref(false);
const lastTimeEndShiftEnded = ref("");
const startShift = ref("");
const endShift = ref("");
const showSuccess = ref(false);
const successMessage = ref("");
const showMenu = ref(false);
const router = useRouter();
const { user } = useAuth();

const goThisWeek = () => {
  showMenu.value = false;
  router.push("/schedule/current");
};

const goNextWeek = () => {
  showMenu.value = false;
  router.push("/schedule/next");
};

let successTimeout: ReturnType<typeof setTimeout> | null = null;

const notifications = reactive<{ id: number; message: string; type: "success" | "error" }[]>([]);
let notificationId = 0;


function addNotification(message: string, type: "success" | "error" = "success") {
  const id = notificationId++;
  notifications.push({ id, message, type });

  // Auto-remove after 3s
  setTimeout(() => {
    const index = notifications.findIndex((n) => n.id === id);
    if (index !== -1) notifications.splice(index, 1);
  }, 3000);
}

// Props from parent
const props = defineProps({
  weeklySchedule: {
    type: Object,
    required: true,
  },
});

const dayOrder: Record<string, number> = {
  Monday: 1,
  Tuesday: 2,
  Wednesday: 3,
  Thursday: 4,
  Friday: 5,
  Saturday: 6,
  Sunday: 7,
};

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
  return days[new Date().getDay()];
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
    return { name, expanded: false, slots: [], date };
  });
};

const week = reactive(createEmptyWeek());

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

// Remove schedule from DB
const deleteSplitSchedule = async (slotId: number, day: string) => {
  if (!slotId) return;

  const today = new Date();
  const todayStr = today.toISOString().split("T")[0];

  const scheduleDay = week.find(d => d.name === day)?.date;
  if (!scheduleDay) return;

  const scheduleDayStr = scheduleDay.toISOString().split("T")[0];

  if (scheduleDayStr < todayStr) {
    alert("You cannot delete schedules from past days.");
    return;
  }
  
  const confirmed = window.confirm(
    "Are you sure you want to delete this schedule?"
  );
  if (!confirmed) return;

  try {
    await fetchDeleteSlot(slotId);

    //ONLY update the source of truth
    props.weeklySchedule.schedule[day] =
      props.weeklySchedule.schedule[day].filter(
        (sched: any) => sched.id !== slotId
      );

    // SUCCESS NOTIFICATION (multiple toasts still supported)
    if (successTimeout) clearTimeout(successTimeout);

    addNotification("Schedule deleted successfully", "success");
    showSuccess.value = true;

    successTimeout = setTimeout(() => {
      showSuccess.value = false;
    }, 3000);
  } catch (e) {
    console.error("Failed to delete slot:", e);
    addNotification("Failed to delete schedule", "error");
  }
};


const handleAddSchedule = (day: string, today: string) => {
  const dailySchedule = props.weeklySchedule.schedule[day];

  const dayIndex = dayOrder[day];
  const todayIndex = dayOrder[today];

  if (dayIndex === undefined || todayIndex === undefined) return;

  if (dayIndex < todayIndex) {
    alert(`Unable to add schedule from previous day.`);
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
    if (toMinutes12h(startShift.value) - toMinutes12h(lastTimeOfShift) <= 30) {
      return alert(
        "Unable to add start shift, driver's shift interval atleast 30 minutes."
      );
    }
  }
};

// Check split break interval
function toMinutes12h(time: string) {
  const match = time.match(/(\d+):(\d+)(am|pm)/i);
  if (!match) return 0;

  let [, h, m, period] = match;
  let hour = Number(h);
  const minutes = Number(m);

  if (period.toLowerCase() === "pm" && hour !== 12) hour += 12;
  if (period.toLowerCase() === "am" && hour === 12) hour = 0;

  return hour * 60 + minutes;
}

const removeHourAndMinueInTime = (time: string) => {
  return time.replace("h ", ":").replace("m", "");
};

const populateDailySchedule = (
  day: string,
  id: number,
  start: string,
  end: string
) => {
  // Ensure the day exists in the schedule
  if (!props.weeklySchedule.schedule[day]) {
    props.weeklySchedule.schedule[day] = [];
  }

  props.weeklySchedule.schedule[day].push({
    id,
    time: formatTime(start, end),
  });
};

// Submit new added drivers schedule
async function submitNewShift(dayDate: string) {
  if (!startShift.value) return alert("Please provide start shift.");
  if (!endShift.value) return alert("Please provide end shift.");

  let endDayDate = dayDate;

  const isOvernight = endShift.value < startShift.value && endShift.value !== "00:00";
  // overnight shift
  if (isOvernight) {
    const date = new Date(dayDate);
    date.setDate(date.getDate() + 1);
    endDayDate = date.toISOString().split("T")[0] ?? dayDate;
  }

  loading.value = true;
  const today = dayToAddSchedule.value;

  try {
    const response: any = await addSchedule(
      `${dayDate} ${startShift.value}:00`,
      `${endDayDate} ${endShift.value}:00`
    );

    if (!response.success) {
      addNotification("Failed to add new shift", "error");
      return;
    }

    //source of truth
    if (isOvernight) {

      populateDailySchedule(today, response.slot_id[0], startShift.value, "00:00");

      const index = daysOfTheWeek.indexOf(today);
      const nextDay = daysOfTheWeek[index + 1];

      if (nextDay) {
        populateDailySchedule(nextDay, response.slot_id[1], "00:00", endShift.value);
      }
    } else {
      populateDailySchedule(today, response.slot_id[0], startShift.value, endShift.value);
    }

    // reset form
    startShift.value = "";
    endShift.value = "";

    // success toast
    if (successTimeout) clearTimeout(successTimeout);

    addNotification("Schedule added successfully", "success");
    showSuccess.value = true;

    successTimeout = setTimeout(() => {
      showSuccess.value = false;
    }, 3000);
  } catch (err) {
    console.error(err);
    addNotification("An unexpected error occurred", "error");
  } finally {
    isDisabled.value = false;
    loading.value = false;
    dayToAddSchedule.value = "";
  }
}

// Change "13:00" to "01:00pm"
const formatTime = (start: string, end: string) => {
  function to12Hour(time: any) {
    const [hourStr, minStr] = time.split(":");
    let hour = parseInt(hourStr, 10);
    const minute = minStr;
    const period = hour >= 12 ? "pm" : "am";

    if (hour === 0)
      hour = 12;
    else if (hour > 12) hour -= 12;

    return `${hour.toString().padStart(2, "0")}:${minute}${period}`;
  }

  return `${to12Hour(start)} - ${to12Hour(end)}`;
};

// Close add shift form
const closeAddForm = () => {
  dayToAddSchedule.value = "";
};

const dailyHours = computed<Record<string, string>>(() => {
  const totals: Record<string, string> = {};

  for (const day of daysOfTheWeek) {
    const schedules = props.weeklySchedule.schedule?.[day] ?? [];
    let totalMinutes = 0;

    for (const sched of schedules) {
      if (!sched?.time) continue;

      // Normalize split
      const parts = sched.time.split("-").map((t: string) => t.trim());
      if (parts.length !== 2) continue;

      const startMin = toMinutes12h(parts[0]);
      const endMin = toMinutes12h(parts[1]);

      if (isNaN(startMin) || isNaN(endMin)) continue;

      // handle overnight
      totalMinutes +=
        endMin >= startMin
          ? endMin - startMin
          : endMin + 24 * 60 - startMin;
    }

    const h = Math.floor(totalMinutes / 60);
    const m = totalMinutes % 60;
    totals[day] = `${h}h ${m}m`;
  }

  return totals;
});

const hours = computed(() => ({
  dailyTotals: dailyHours.value,
}));

const totalWeeklyHours = computed(() => {
  let total = 0;

  for (const value of Object.values(dailyHours.value)) {
    const [h, m] = value.replace("h", "").replace("m", "").split(" ").map(Number);
    total += h * 60 + m;
  }

  return `${Math.floor(total / 60)}h ${total % 60}m`;
});

const normalizedWeeklySchedule = computed(() => {
  const result: Record<string, { start: string; end: string }[]> = {};
  for (const day of Object.keys(props.weeklySchedule.schedule)) {
    result[day] = props.weeklySchedule.schedule[day].map((s: any) => {
      if (s.start && s.end) return s;
      
      const parts = s.time?.split("-").map((t: string) => t.trim());
      return parts?.length === 2
        ? { start: parts[0], end: parts[1] }
        : { start: "00:00am", end: "00:00am" };
    });
  }
  return result;
});

const formattedMinHours = computed(() => {
  const numValue = Number(user.value?.minimumScheduledHours);

  if (isNaN(numValue)) {
    return "0";
  }

  return `${Math.floor(numValue)}h`;
});

const formattedAcceptanceRate = computed(() => {
  const rawValue = user.value?.acceptanceRate;
  const numValue = Number(rawValue);

  if (rawValue == null || numValue === 0 || isNaN(numValue)) {
    return "N/A";
  }

  return `${Math.floor(numValue)}%`;
});

const acceptanceRateColor = computed(() => {
  const current = Number(user.value?.acceptanceRate || 0);
  const needed = Number(user.value?.acceptanceRateNeeded || 0);

  if (current == 0){
    return 'text-gray-500';
  }

  if (current < needed) {
    return 'text-red-500';
  }
  
  if (current >= needed && current < (needed + 6)) {
    return 'text-yellow-500';
  }
  
  return 'text-green-500';
});

const displayDailyTotal = (value: string) => {
  return /^0h\s*0+m$/.test(value) ? "OFF" : value;
};
</script>

<template>
  <div
    class="flex flex-col w-full h-auto shadow-lg rounded-xl p-3 border lg:mt-10 dark:bg-black shadow-blue"
  >
    <!-- Success Notification -->
    <TransitionGroup name="toast" tag="div" class="fixed top-6 right-6 z-50 space-y-2">
      <div
        v-for="toast in notifications"
        :key="toast.id"
        :class="[
          'px-4 py-2 rounded-lg shadow-lg text-sm text-white',
          toast.type === 'success' ? 'bg-green-500' : 'bg-red-500'
        ]"
      >
        {{ toast.message }}
      </div>
    </TransitionGroup>
    <!-- Date Range -->
    <div class="flex justify-between">
      <!-- LEFT SIDE -->
  <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center w-full gap-4">
  
  <div class="relative flex w-full items-center justify-between lg:inline-flex lg:w-auto">
  
  <div class="relative">
    <button
      class="flex items-center gap-1 text-blue-600 font-semibold text-md md:text-lg sm:text-sm dark:text-gray-300"
      @click="showMenu = !showMenu"
    >
      {{ dateRange }}
      <ChevronDown class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': showMenu }" />
    </button>

    <div v-if="showMenu" class="absolute left-0 lg:left-full lg:ml-2 mt-2 lg:mt-0 top-full lg:top-1/2 lg:-translate-y-1/2 bg-white dark:bg-gray-800 border rounded-lg shadow-lg z-50">
      <button class="block w-full px-4 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-700 whitespace-nowrap" @click="goThisWeek">This Week Schedule</button>
      <button class="block w-full px-4 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-700 whitespace-nowrap" @click="goNextWeek">Next Week Schedule</button>
    </div>
  </div>

  <p class="text-gray-400 dark:text-gray-300 text-xs md:text-sm lg:hidden">
    Total: <b>{{ totalWeeklyHours }}</b>
  </p>
</div>

  <div class="flex w-full lg:w-auto lg:gap-x-4 justify-between items-center border-t lg:border-none pt-3 lg:pt-0 border-gray-700">
    
    <p class="text-gray-400 dark:text-gray-300 text-xs md:text-sm">
      Acceptance Rate: <b :class="acceptanceRateColor">{{ formattedAcceptanceRate }}</b>
    </p>

    <div class="flex items-center gap-1">
  <div class="relative group">
    <Info color="green" class="w-4 h-4 cursor-pointer" />
    
    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-56 p-2 text-[10px] text-white bg-[#0078d4] dark:bg-gray-700 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none z-10">
      This is based on your submission when you signed up, should you need to change this please email <b>mary@ydriveapp.com</b>.
    </div>
  </div>
    <p class="text-gray-400 dark:text-gray-300 text-xs md:text-sm whitespace-nowrap">
      Required: <b>{{ formattedMinHours }}</b>
    </p>
  </div>
    <p class="hidden lg:block text-gray-400 dark:text-gray-300 text-xs md:text-sm">
      Total: <b>{{ totalWeeklyHours }}</b>
    </p>
  </div>
</div>
  </div>

    <!-- Days List -->
    <div
      class="py-4 space-y-4 lg:flex gap-2 w-full lg:justify-between dark:bg-[#18181b]"
    >
      <div
        v-for="day in week"
        :key="day.name"
        class="pb-2 border p-1 rounded-lg w-full shadow-blue"
      >
        <!-- Day Header -->
        <div
          class="flex justify-between rounded-xl shadow-lg items-center px-2 py-1 cursor-pointer "
          :class="{
            'bg-[#0078d4] dark:bg-gray-700 text-white': day.name === todayName,
            'color-blue dark:bg-gray-900 dark:text-gray-300 shadow-lg border':
              day.name !== todayName,
          }"
        >
          <!-- Day Name on the left -->
          <div class="flex gap-2 place-items-center w-full">
            <span
              class="text-xs md:text-xs sm:text-xs font-semibold dark:text-gray-300"
              >{{ day.name }}</span
            >
          </div>

          <!-- Date and toggle on the right -->
          <div class="flex items-center space-x-3 md:space-x-2 sm:space-x-1">
            <span
              class="text-sm text-[#0078d4] md:text-[12px] sm:text-[9px] whitespace-nowrap"
              :class="{
                'dark:bg-gray-700 text-white dark:text-text-300':
                  day.name === todayName,
                'color-blue ': day.name !== todayName,
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
              class="text-lg leading-none md:text-sm sm:text-base"
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

        <!-- Schedules -->
        <div
          v-for="schedule in props.weeklySchedule.schedule[day.name]"
          :key="schedule.id"
          class="w-full flex justify-between rounded-xl shadow-blue items-center px-2 py-1 cursor-pointer mt-[.5px] border shadow"
          :class="{
            'bg-[#0078d4] dark:bg-gray-700 text-white': day.name === todayName,
            'bg-white dark:bg-gray-900 text-[#0078d4] dark:text-white':
              day.name !== todayName,
          }"
        >
          <span class="text-[11px] whitespace-nowrap">
            {{ schedule.time }}
          </span>

          <button
            class="text-xl leading-none md:text-xl sm:text-base ml-3"
            @click="deleteSplitSchedule(schedule.id, day.name)"
          >
            <Minus size="12" />
          </button>
        </div>

        <DailyTotal
          :visible="hours.dailyTotals[day.name] !== '24h 00m'"
          :daily-total="displayDailyTotal(hours.dailyTotals[day.name])"
        />

        <!--End schedules-->

        <!-- Add Split shoft form-->
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
          :is-disabled="isDisabled"
          :initial-start="startShift"
          :initial-end="endShift"
          :schedule-day="day.date"
          :last-time-shift-end="lastTimeEndShiftEnded"
          :booked-slots="normalizedWeeklySchedule[day.name]" 
          @close="closeAddForm"
          @submit="submitNewShift(formatDayDate(day.date))"
          @update:start="(val: any) => (startShift = val)"
          @update:end="(val: any) => (endShift = val)"
        />
        </Transition>
        <!-- End Split shoft form-->
      </div>
    </div>
  </div>
</template>
