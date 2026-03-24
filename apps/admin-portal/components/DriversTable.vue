<script setup lang="ts">
import { ref, computed, watch } from "vue";
import { useFetch } from "#imports";
import {
  Loader,
  Wrench,
  X,
  Download,
} from "lucide-vue-next";
import * as XLSX from 'xlsx';
import Modal from "~/components/Modal.vue";
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from "@/components/ui/alert-dialog";
const vendor = useVendor();

const props = defineProps<{ drivers: Array<any> }>();
const emit = defineEmits<{
  (e: "select-driver", driver: any): void;
}>();

const selectedCity = ref("");
const searchDriver = ref("");
const selectedDriverId = ref(0);
const showUpdateForm = ref(false);
const selectedDriver = ref<any>(null); 
const activeFilter = ref(""); 
const sortField = ref(""); 
const sortOrder = ref("asc");
const scheduleFilter = ref(""); 
const acceptanceRange = ref({ min: 0, max: 100 }); 
const cities = ref([
  "peterborough",
  "sudbury",
  "medicine hat",
  "cobourg",
  "lindsay",
  "lethbridge",
  "huntsville",
  "grande prairie",
]);

const currentPage = ref(1);
const itemsPerPage = ref(10);
const loading = ref(false);
const showModal = ref(false);
const addingToNoOpps = ref(false);
const localDrivers = ref<any[]>([]);

watch(
  () => props.drivers,
  (drivers) => {
    const list = Array.isArray(drivers) ? drivers : [];

    localDrivers.value = list.map((d) => ({
      id: d.id ?? 0,
      autofleetDriverId: d.autofleetDriverId ?? 0,
      firstName: d.firstName ?? "",
      lastName: d.lastName ?? "",
      name: `${d.firstName ?? ""} ${d.lastName ?? ""}`,
      schedule: d.hasCurrentSchedule ?? false,
      nextSchedule: d.hasNextSchedule ?? false,
      acceptance: d.acceptanceRate ?? 0,
      acceptanceNeeded: d.acceptanceRateNeeded ?? 0,
      scheduledHours: d.minimumScheduledHours ?? 0,
      expiredOffers: d.expiredOffers ?? 0,
      rejectedOffers: d.rejectedOffers ?? 0,
      enabled: d.enabled ?? true,
      city: d.city ?? "",
      schedules: d.schedules,
      isDelinquent: d.isDelinquent,
    }));
  },
  { immediate: true },
);

const filteredDrivers = computed(() => {
  let drivers = localDrivers.value.filter((d) => {

    const hasValidCity = d.city && d.city !== null && d.city !== '';
    
    const matchesCity = selectedCity.value
      ? d.city.toLowerCase() === selectedCity.value.toLowerCase()
      : true;

    const matchesSearch = d.name
      .toLowerCase()
      .includes(searchDriver.value.toLowerCase());
    
    const matchesActive = activeFilter.value
      ? activeFilter.value === "active"
        ? !d.isDelinquent
        : d.isDelinquent
      : true;
    
    const matchesSchedule = scheduleFilter.value
      ? scheduleFilter.value === "scheduled"
        ? d.schedule
        : !d.schedule
      : true;
    
    const matchesAcceptanceRange = 
      d.acceptance >= acceptanceRange.value.min && 
      d.acceptance <= acceptanceRange.value.max;
    
    return hasValidCity && matchesCity && matchesSearch && matchesActive && matchesSchedule && matchesAcceptanceRange;
  });
  
   if (sortField.value) {
    drivers.sort((a, b) => {
      let aValue, bValue;
      
      switch (sortField.value) {
        case "acceptance":
          aValue = a.acceptance || 0;
          bValue = b.acceptance || 0;
          break;
        case "rejected":
          aValue = a.rejectedOffers || 0;
          bValue = b.rejectedOffers || 0;
          break;
        case "ignored":
          aValue = a.expiredOffers || 0;
          bValue = b.expiredOffers || 0;
          break;
        case "schedule":
          aValue = a.schedule ? 1 : 0;
          bValue = b.schedule ? 1 : 0;
          break;
        default:
          return 0;
      }
      
      if (sortOrder.value === "asc") {
        return aValue - bValue;
      } else {
        return bValue - aValue;
      }
    });
  }
  
  return drivers;
});

const totalPages = computed(() =>
  Math.ceil(filteredDrivers.value.length / itemsPerPage.value),
);

const paginatedDrivers = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredDrivers.value.slice(start, end);
});

const visiblePages = computed(() => {
  const total = totalPages.value;
  const current = currentPage.value;
  const pages: number[] = [];

  if (total <= 3) {
    for (let i = 1; i <= total; i++) pages.push(i);
  } else {
    let start = current - 1;
    let end = current + 1;
    if (start < 1) {
      start = 1;
      end = 3;
    }
    if (end > total) {
      end = total;
      start = total - 2;
    }
    for (let i = start; i <= end; i++) pages.push(i);
  }

  return pages;
});

const goToPage = (page: number) => {
  if (page < 1 || page > totalPages.value) return;
  currentPage.value = page;
};


watch([selectedCity, searchDriver, activeFilter, scheduleFilter, acceptanceRange, sortField, sortOrder], () => {
  currentPage.value = 1;
});

watch(selectedCity, () => {
  searchDriver.value = "";
  currentPage.value = 1;
});

const closeUpdateForm = () => {
  selectedDriverId.value = 0;
  showUpdateForm.value = false;
};

const handleShowUpdateForm = (driverData: any, event: any) => {
  showUpdateForm.value = true;
  selectedDriverId.value = driverData.id;
  
  // Store the click position for the form
  const rect = event.currentTarget.getBoundingClientRect();
  selectedDriver.value = {
    ...driverData,
    position: {
      top: rect.top - 230,
      left: rect.left
    }
  };
};

async function submitDriverUpdate(driver: any) {
  loading.value = true;
  try {
    const payload = {
      minimum_scheduled_hours: driver.scheduledHours,
      acceptance_rate: driver.acceptanceNeeded,
    };

    await $fetch(`/api/v1/driver/${driver.id}/update-schedule`, {
      method: "PATCH",
      body: payload,
    });
  } catch (err: any) {
    alert(err.message);
  } finally {
    loading.value = false;
    showUpdateForm.value = false;
    showModal.value = false;
    selectedDriverId.value = 0;
  }
}

const handleShowUpdateModal = (driverData: any) => {
  selectedDriverId.value = driverData.id;
  showModal.value = true;
};

const moveDriverVendor = async (driver: any) => {
  const { id, city, autofleetDriverId, isDelinquent, enabled } = driver;

  try {
    addingToNoOpps.value = true;
    selectedDriverId.value = id;

    if (!isDelinquent) {
      await vendor.update(id, city, autofleetDriverId);

      localDrivers.value = localDrivers.value.map((d) =>
        d.id === id ? { ...d, isDelinquent: true } : d,
      );
    } else {
      await vendor.revert(id, city, autofleetDriverId);

      localDrivers.value = localDrivers.value.map((d) =>
        d.id === id ? { ...d, isDelinquent: false } : d,
      );
    }
  } catch (error) {
    console.error(error);
  } finally {
    addingToNoOpps.value = false;
    selectedDriverId.value = 0;
  }
};

const perPageOptions = [
  { label: "Show 10", value: 10 },
  { label: "Show 50", value: 50 },
  { label: "Show 100", value: 100 },
  { label: "Show All", value: localDrivers.value.length },
];

function updateItemsPerPage(event: Event) {
  const target = event.target as HTMLSelectElement;
  itemsPerPage.value = Number(target.value);
}

function handleSort(field: string) {
  if (sortField.value === field) {
    sortOrder.value = sortOrder.value === "asc" ? "desc" : "asc";
  } else {
    sortField.value = field;
    sortOrder.value = "asc";
  }
}

function handleScheduleFilter() {
  if (scheduleFilter.value === "") {
    scheduleFilter.value = "scheduled";
  } else if (scheduleFilter.value === "scheduled") {
    scheduleFilter.value = "unscheduled";
  } else {
    scheduleFilter.value = "";
  }
}

function getSortIcon(field: string) {
  if (sortField.value !== field) return "↕";
  return sortOrder.value === "asc" ? "↑" : "↓";
}

function getScheduleFilterIcon() {
  if (scheduleFilter.value === "") return "↕";
  return scheduleFilter.value === "scheduled" ? "✔" : "✖";
}

function exportToExcel() {
  const headers = [
    'Driver ID',
    'First Name',
    'Last Name',
    'Full Name',
    'City',
    'Scheduled Today',
    'Acceptance Rate (%)',
    'Acceptance Target (%)',
    'Rejected Offers',
    'Ignored Offers',
    'Minimum Scheduled Hours',
    'Status',
    'Is Delinquent'
  ];

  const rows = filteredDrivers.value.map(driver => [
    driver.id,
    driver.firstName || '',
    driver.lastName || '',
    driver.name || '',
    driver.city || 'N/A',
    driver.schedule ? 'Yes' : 'No',
    driver.acceptance || 0,
    driver.acceptanceNeeded || 0,
    driver.rejectedOffers || 0,
    driver.expiredOffers || 0,
    driver.scheduledHours || 0,
    driver.isDelinquent ? 'Inactive' : 'Active',
    driver.isDelinquent ? 'Yes' : 'No'
  ]);

  const wsData = [headers, ...rows];

  const wb = XLSX.utils.book_new();
  const ws = XLSX.utils.aoa_to_sheet(wsData);
  XLSX.utils.book_append_sheet(wb, ws, "Drivers");

  const excelBuffer = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });

  const blob = new Blob([excelBuffer], { 
    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' 
  });
  const link = document.createElement('a');
  const url = URL.createObjectURL(blob);

  link.setAttribute('href', url);
  link.setAttribute('download', `drivers-export-${new Date().toISOString().split('T')[0]}.xlsx`);
  link.style.visibility = 'hidden';

  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

</script>

<template>
  <div
    class="flex flex-col w-full relative rounded-xl lg:p-6 md:p-3 bg-white lg:shadow-lg md:shadow lg:border md:border mt-6 md:mt-3 z-10 dark:bg-[#262728] lg:overflow-hidden overflow-x-scroll"
  >
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 lg:gap-4.5 lg:space-y-[1px] p-2">

      <div class="relative w-full lg:w-1/4">
        <select
          v-model="selectedCity"
          class="w-full capitalize no-underline background-light-blue appearance-none rounded-lg px-4 py-2 text-sm bg-white dark:bg-[#262728] border dark:text-white/50"
        >
          <option value="">All Cities</option>
          <option v-for="city in cities" :key="city" :value="city">
            {{ city.replace("_", " ") }}
          </option>
        </select>

        <svg
          class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <path d="M6 9l6 6 6-6" />
        </svg>
      </div>

      <div class="relative w-full lg:w-1/4">
        <select
          v-model="activeFilter"
          class="w-full capitalize no-underline background-light-blue appearance-none rounded-lg px-4 py-2 text-sm bg-white dark:bg-[#262728] border dark:text-white/50"
        >
          <option value="">All Status</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>

        <svg
          class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <path d="M6 9l6 6 6-6" />
        </svg>
      </div>

      <div class="relative w-full lg:w-1/4 flex items-center gap-2">
        <input
          type="number"
          v-model.number="acceptanceRange.min"
          placeholder="Min %"
          min="0"
          max="100"
          class="w-1/2 px-2 py-2 text-sm background-light-blue bg-white dark:bg-[#262728] border rounded-lg dark:text-white/50"
        />
        <input
          type="number"
          v-model.number="acceptanceRange.max"
          placeholder="Max %"
          min="0"
          max="100"
          class="w-1/2 px-2 py-2 text-sm background-light-blue bg-white dark:bg-[#262728] border rounded-lg dark:text-white/50"
        />
      </div>

      <div class="relative w-full lg:w-1/4 dark:bg-[#262728]">
        <svg
          class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none outline-none"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"
          />
        </svg>
        <input
          v-model="searchDriver"
          type="text"
          placeholder="Search Driver"
          class="w-full background-light-blue rounded-lg px-4 py-2 pl-8 text-sm bg-white border dark:bg-[#262728]"
        />
      </div>

      <div class="flex items-center w-full lg:w-auto">
        <button
          @click="exportToExcel"
          class="flex items-center justify-center gap-2 px-4 py-2.5 text-sm bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors w-full lg:w-auto"
          title="Export to Excel"
        >
          <Download class="w-4 h-4 dark:text-white/50" />
          <span class="hidden lg:inline">Export</span>
        </button>
      </div>
    </div>

    <div class="-top-8 left-6 p-4 rounded z-20 mt-3">
      <table class="w-full min-w-full">
        <thead class="border-b">
          <tr class="text-left text-gray-500">
            <th
              class="px-2 py-1 text-left text-gray-500 text-xs md:text-sm pl-7"
            >
              Driver
            </th>
            <th 
              @click="handleScheduleFilter"
              class="px-2 py-1 text-center text-xs md:text-sm cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              Schedule {{ getScheduleFilterIcon() }}
            </th>
            <th
              @click="handleSort('acceptance')"
              class="px-2 py-1 text-center text-xs md:text-sm whitespace-nowrap cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              Acceptance % {{ getSortIcon('acceptance') }}
            </th>
            <th 
              @click="handleSort('rejected')"
              class="px-2 py-1 text-center text-xs md:text-sm cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              Rejected {{ getSortIcon('rejected') }}
            </th>
            <th 
              @click="handleSort('ignored')"
              class="px-2 py-1 text-center text-xs md:text-sm cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              Ignored {{ getSortIcon('ignored') }}
            </th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="driver in paginatedDrivers"
            :key="driver.id"
            class="border-b cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700"
            @click="$emit('select-driver', driver)"
          >
            <td
              ref="driverCell"
              class="py-3 text-xs md:text-sm font-medium w-full max-w-[280px] flex gap-2 items-start group relative"
            >
              <Wrench
                :size="18"
                color="#0078d4"
                class="hidden lg:block flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity"
                @click.stop="handleShowUpdateForm(driver, $event)"
              />

              <Wrench
                :size="18"
                color="#0078d4"
                class="lg:hidden flex-shrink-0 opacity-100"
                @click.stop="handleShowUpdateModal(driver)"
              />

              <span
                class="dark:text-white/70 block truncate max-w-full relative z-10"
                :title="driver.name"
              >
                {{ driver.name }}
              </span>

              <div
                v-if="showUpdateForm && selectedDriverId === driver.id && selectedDriver"
                @click.stop
                class="fixed z-[9999] w-[260px] bg-white dark:bg-[#262728] p-4 shadow-lg border rounded-lg space-y-3 text-start"
                :style="{
                  top: selectedDriver.position.top + 'px',
                  left: selectedDriver.position.left + 'px'
                }"
              >
                <label class="text-gray-500">Hours needed</label>
                <input
                  type="number"
                  v-model.number="driver.scheduledHours"
                  class="border p-2 rounded w-full"
                />

                <label class="text-gray-500">Acceptance rate needed</label>
                <input
                  type="number"
                  v-model.number="driver.acceptanceNeeded"
                  class="border p-2 rounded w-full"
                />

                <div class="flex gap-2 mt-3">
                  <button
                    @click="closeUpdateForm()"
                    class="w-1/3 py-2 bg-gray-200 dark:bg-gray-600 rounded-lg flex justify-center"
                  >
                    <X />
                  </button>

                  <button
                    :disabled="loading"
                    @click="submitDriverUpdate(driver)"
                    class="w-full py-2 rounded-lg text-white"
                    :class="
                      loading
                        ? 'bg-gray-400 cursor-not-allowed'
                        : 'bg-[#0078D4]'
                    "
                  >
                    {{ loading ? "Saving..." : "Save" }}
                  </button>
                </div>
              </div>
            </td>

            <td class="px-2 py-1 text-center text-xs md:text-sm">
              <span v-if="driver.schedule" class="color-blue">✔</span>
              <span v-else class="text-red-500">✖</span>
            </td>

            <td
              :class="[
                driver.acceptance < driver.acceptanceNeeded
                  ? 'text-red-500'
                  : 'color-blue',
                'text-center',
                'px-2',
                'py-1',
                'text-xs',
                'md:text-sm',
              ]"
            >
              {{ driver.acceptance }}%
            </td>

            <td class="px-2 py-1 text-center text-xs md:text-sm">
              {{ driver.rejectedOffers }}
            </td>
            <td class="px-2 py-1 text-center text-xs md:text-sm">
              {{ driver.expiredOffers }}
            </td>

            <td class="text-center align-middle" @click.stop>
              <AlertDialog>
                <AlertDialogTrigger asChild>
                  <div
                    v-if="addingToNoOpps && selectedDriverId === driver.id"
                    class="pl-6"
                  >
                    <Loader class="animate-spin" />
                  </div>

                  <div
                    v-else
                    class="w-10 h-5 mx-auto flex items-center rounded-full transition-colors duration-300 cursor-pointer"
                    :class="
                      driver.isDelinquent ? 'bg-gray-300' : 'background-blue'
                    "
                  >
                    <div
                      class="w-4 h-4 bg-white rounded-full shadow transform transition-transform duration-300"
                      :class="
                        driver.isDelinquent ? 'translate-x-1' : 'translate-x-5'
                      "
                    ></div>
                  </div>
                </AlertDialogTrigger>
                <AlertDialogContent>
                  <AlertDialogHeader>
                    <AlertDialogTitle>Are you sure ?</AlertDialogTitle>
                    <AlertDialogDescription>
                      {{
                        driver.isDelinquent
                          ? "This driver will be remove from it's NO OPPS vendor and OPPS board will be visible to his end."
                          : "Opportunity board will be hidden to this driver and he will be added to NO OPPS vendors lists."
                      }}
                    </AlertDialogDescription>
                  </AlertDialogHeader>
                  <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <AlertDialogAction @click="moveDriverVendor(driver)"
                      >Continue</AlertDialogAction
                    >
                  </AlertDialogFooter>
                </AlertDialogContent>
              </AlertDialog>
            </td>

            <Modal
              v-model:show="showModal"
              v-if="selectedDriverId === driver.id"
              @clik.stop
            >
              <h2 class="text-2xl font-bold mb-6">Update Driver</h2>
              <div
                class="bg-white dark:bg-[#262728] space-y-3 text-start"
                v-if="selectedDriverId === driver.id"
                @click.stop
              >
                <label for="" class="text-gray-500 w-full text-lg"
                  >Hours needed</label
                >
                <input
                  type="number"
                  v-model.number="driver.scheduledHours"
                  class="border p-3 rounded-lg w-full"
                />
                <label for="" class="text-gray-500 text-lg"
                  >Acceptance rate needed</label
                >
                <input
                  type="number"
                  v-model.number="driver.acceptanceNeeded"
                  class="border p-3 rounded-lg w-full"
                />
                <div class="lg:flex gap-2 mt-3 space-y-3">
                  <button
                    :disabled="loading"
                    class="w-full py-3 bg-[#0078D4] rounded-lg text-white"
                    :class="
                      loading
                        ? 'bg-gray-400 cursor-not-allowed'
                        : 'bg-[#0078D4]'
                    "
                    @click.stop="submitDriverUpdate(driver)"
                  >
                    {{ loading ? "Saving..." : "Save" }}
                  </button>
                  <button
                    @click.stop="closeUpdateForm()"
                    class="lg:w-1/3 w-full py-3 bg-gray-100 flex place-items-center justify-center rounded-lg text-gray-800 text-center flex place-items-center dark:border dark:border-gray-500 dark:bg-[#262728] dark:text-white/50"
                  >
                    Close
                  </button>
                </div>
              </div>
            </Modal>
          </tr>
        </tbody>
      </table>

      <div class="flex justify-between place-items-center">
        <div class="flex gap-2 mt-5">
          <div class="relative flex items-center">
           <select
              v-model="itemsPerPage"
              @change="updateItemsPerPage"
              class="w-auto capitalize no-underline background-light-blue appearance-none rounded-lg px-4 py-2 text-sm bg-white dark:bg-[#262728] border dark:text-white/50"
            >
              <option
                v-for="option in perPageOptions"
                :key="option.value"
                :value="option.value"
              >
                {{ option.label }}
              </option>
            </select>

            <svg
              class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <path d="M6 9l6 6 6-6" />
            </svg>
          </div>
        </div>

        <div
          v-if="filteredDrivers.length > itemsPerPage"
          class="flex justify-center items-center space-x-2 lg:mt-5 mt-10 z-10"
        >
          <button
            class="px-3 py-1 rounded border disabled:opacity-50"
            :class="currentPage > 1 ? 'color-blue hover:text-blue-600' : ''"
            :disabled="currentPage === 1"
            @click="goToPage(currentPage - 1)"
          >
            &lt;
          </button>

          <button
            v-for="page in visiblePages"
            :key="page"
            class="px-3 py-1 rounded border hover:bg-ydrive-blue hover:text-white transition-colors"
            :class="
              currentPage === page ? 'bg-[#0078d4] text-white' : 'text-blue-500'
            "
            @click="goToPage(page)"
          >
            {{ page }}
          </button>

          <!-- Next button -->
          <button
            class="px-3 py-1 text-lg rounded border disabled:opacity-50 disabled:cursor-not-allowed"
            :class="
              currentPage < totalPages ? 'color-blue hover:hvr-color-blue' : ''
            "
            :disabled="currentPage === totalPages"
            @click="goToPage(currentPage + 1)"
          >
            &gt;
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
