<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useZoho } from "#imports"
import { fetchAllDrivers } from "~/lib/api/drivers";
import DriversTable from "~/components/DriversTable.vue";
import DriverSchedule from "~/components/DriverSchedule.vue";
import { getMondayOfCurrentWeek } from "~/utils/date";
import AdminTabs from "~/components/tabs/admin/AdminTabs.vue";
import Scheduler from "../driver/scheduler.vue";
const selectedDriverId = useSelectedDriverId();
const activePage = useCurrentPage();
const activeSubPage = useCurrentSubPage();

definePageMeta({
  layout: "auth",
  layoutTransition: {
    name: "slide-up",
    mode: "out-in",
  },
  middleware: ["auth"],
});

const schedule = useSchedule();
const userReady = ref(false);

// Current page state
const currentPage = ref(activePage.value ?? "home");
const currentSubPage = ref(activeSubPage.value ?? "admin-schedule");

// Selected driver
const selectedDriver = ref(null);

// Weekly schedule
const weeklySchedule = ref({});
const error = ref(null);

// Drivers list
const drivers = useState<any[]>("drivers", () => []);
const showOnMobile = ref(false);

// Load drivers
const loadDrivers = async () => {
  try {
    const data = await fetchAllDrivers();

    drivers.value = data.drivers;
  } catch (err) {
    console.error("Failed to fetch drivers:", err);
  }
};

// Driver select handler
const handleDriverSelect = async (driver: any) => {
  selectedDriver.value = driver;
  currentPage.value = "schedule";

  selectedDriverId.value = driver.id.toString();

  const driverId = driver.id;
  const startDate = getMondayOfCurrentWeek() ?? "";

  try {
    // Remove this line: loading.value = true;
    const res = await schedule.getWeeklySchedule(driverId, startDate);
    weeklySchedule.value = res;
  } catch (err: any) {
    error.value = err.message;
  }
};

// Deselect driver
const deselectDriver = () => {
  selectedDriver.value = null;
  weeklySchedule.value = {};
  selectedDriverId.value = null;
  currentPage.value = "schedule";
};

// --------------------
// Mount
// --------------------

const emit = defineEmits<{
  (e: "change-page", page: string): void;
}>();

// Subpages handler
const handleChangeSubpage = (page: string) => {
  currentSubPage.value = page;
  activeSubPage.value = page;
};
</script>

<template>
  <div v-if="currentPage === 'schedule'" class="z-10">
    <div
      class="flex justify-center place-items-center md:flex-col flex-col overflow-auto lg:px-4 px-3 gap-3"
    >
      <AdminTabs
        :currentSubPage="currentSubPage"
        :handleChangeSubpage="handleChangeSubpage"
      />
    </div>

    <div
      v-if="currentSubPage === 'admin-schedule'"
      class="flex flex-col h-full text-center gap-4 mt-6"
    >
      <Scheduler v-if="drivers.length > 0" :drivers="drivers" />
    </div>

    <div class="lg:flex gap-3 w-full">
      <DriversTable
        v-if="currentSubPage === 'admin-audit'"
        :driver="selectedDriver"
        :drivers="drivers"
        @select-driver="handleDriverSelect"
        :class="{
          'hidden sm:block': selectedDriver,
        }"
      />

      <div
        v-if="selectedDriver && currentSubPage === 'admin-audit'"
        class="lg:w-[18%] flex-none md:mt-3 mt-10"
      >
        <DriverSchedule
          :driver="selectedDriver"
          :weeklySchedule="weeklySchedule"
          @deselect="deselectDriver"
        />
      </div>
    </div>
  </div>
</template>

<style></style>
