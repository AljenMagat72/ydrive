<script setup lang="ts">
import { ref, computed, watch } from "vue";
import {
  CheckSquare,
  Square,
  ChevronDown,
  Loader2
} from "lucide-vue-next";

const props = defineProps<{ drivers: Array<any> }>();

const fieldMapping = {
  "Driver's License": "Drivers_License",
  "Car Photo": "Car_Photo",
  "Insurance": "Insurance_Photo",
  "City License": "City_License_Permit",
  "Vehicle Registration": "Vehicle_Ownership",
  "Criminal Record": "Criminal_Vulnerable",
  "Driver's Abstract": "Drivers_Abstract",
  "Safety Certificate": "Vehicle_Safety"
} as const;

type DocumentLabel = keyof typeof fieldMapping;

const documentList: DocumentLabel[] = [
  "Driver's License", "Car Photo", "Insurance",
  "City License", "Vehicle Registration", "Criminal Record",
  "Driver's Abstract", "Safety Certificate"
];

const selectedCity = ref("");
const searchDriver = ref("");
const expandedDriverId = ref<number | string | null>(null);
const selectedDocs = ref<DocumentLabel[]>([]);
const driverDocsData = ref<Record<string, any> | null>(null);

const cities = ref([
  "peterborough", "sudbury", "medicine hat", "cobourg",
  "lindsay", "lethbridge", "huntsville", "grande prairie",
]);

const currentPage = ref(1);
const itemsPerPage = ref(10);
const { fetchZohoDetails, downloadAttachmentsZip, isLoading } = useZoho();

const processedDrivers = computed(() => {
  const list = Array.isArray(props.drivers) ? props.drivers : [];
  return list.map((d) => ({
    id: d.id ?? 0,
    name: `${d.firstName ?? ""} ${d.lastName ?? ""}`.trim() || "Unknown",
    city: d.city ?? "",
    zohoId: d.zodo_id || d.zoho_id || d.zohoId || null
  }));
});

const filteredDrivers = computed(() => {
  return processedDrivers.value.filter((d) => {
    const hasValidCity = d.city && d.city !== '';
    const matchesCity = selectedCity.value ? d.city.toLowerCase() === selectedCity.value.toLowerCase() : true;
    const matchesSearch = d.name.toLowerCase().includes(searchDriver.value.toLowerCase());
    return hasValidCity && matchesCity && matchesSearch;
  });
});

const toggleExpand = async (driver: any) => {
  if (expandedDriverId.value === driver.id) {
    expandedDriverId.value = null;
  } else {
    expandedDriverId.value = driver.id;
    selectedDocs.value = []; 
    driverDocsData.value = null;
    if (driver.zohoId) {
      driverDocsData.value = await fetchZohoDetails(driver.zohoId);
    }
  }
};

const isDocDisabled = (docLabel: string, driver: any): boolean => {
  if (!driver.zohoId || (isLoading.value && !driverDocsData.value)) return true;
  const val = driverDocsData.value?.[fieldMapping[docLabel as DocumentLabel]];
  return Array.isArray(val) ? val.length === 0 : !val;
};

const toggleDoc = (doc: DocumentLabel) => {
  if (selectedDocs.value.includes(doc)) {
    selectedDocs.value = selectedDocs.value.filter(d => d !== doc);
  } else {
    selectedDocs.value.push(doc);
  }
};

const handleChecklistDownload = async (driver: any) => {
  const data = driverDocsData.value;
  if (!driver.zohoId || !data || selectedDocs.value.length === 0) return;

  const fileIds = selectedDocs.value
    .map(label => {
      const val = data[fieldMapping[label]];
      if (Array.isArray(val) && val.length > 0) return val[0].file_Id || val[0].id || val[0].zc_display_value;
      return typeof val === 'string' ? val : null;
    })
    .filter((id): id is string => !!id);

  try {
      await downloadAttachmentsZip(driver.zohoId, fileIds, driver.name);
      
      selectedDocs.value = [];
      expandedDriverId.value = null;
      
    } catch (error) {
      console.error("Download failed", error);
    }
};

const availableDocs = computed(() => {
  if (!driverDocsData.value) return [];
  return documentList.filter(label => {
    const val = driverDocsData.value?.[fieldMapping[label]];
    return Array.isArray(val) ? val.length > 0 : !!val;
  });
});

const isAllSelected = computed(() => availableDocs.value.length > 0 && availableDocs.value.every(doc => selectedDocs.value.includes(doc)));
const selectAllAvailable = () => selectedDocs.value = isAllSelected.value ? [] : [...availableDocs.value];

const totalPages = computed(() => Math.ceil(filteredDrivers.value.length / itemsPerPage.value));
const paginatedDrivers = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return filteredDrivers.value.slice(start, start + itemsPerPage.value);
});

const visiblePages = computed(() => {
  const total = totalPages.value;
  const current = currentPage.value;
  const pages: number[] = [];
  if (total <= 3) { for (let i = 1; i <= total; i++) pages.push(i); } 
  else {
    let start = Math.max(1, current - 1);
    let end = Math.min(total, current + 1);
    if (current === 1) end = 3;
    if (current === total) start = total - 2;
    for (let i = start; i <= end; i++) pages.push(i);
  }
  return pages;
});

const goToPage = (page: number) => { if (page >= 1 && page <= totalPages.value) currentPage.value = page; };

watch([selectedCity, searchDriver], () => { currentPage.value = 1; });

const perPageOptions = computed(() => [
  { label: "Show 10", value: 10 },
  { label: "Show 50", value: 50 },
  { label: "Show 100", value: 100 },
  { label: "Show All", value: filteredDrivers.value.length },
]);
</script>

<template>
  <div class="flex flex-col w-full relative rounded-xl lg:p-6 md:p-3 bg-white lg:shadow-lg md:shadow lg:border md:border mt-6 md:mt-3 z-10 dark:bg-[#262728] lg:overflow-hidden overflow-x-scroll">
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 lg:gap-4.5 p-2">
      <div class="relative w-full lg:w-1/4">
        <select v-model="selectedCity" class="w-full capitalize no-underline background-light-blue appearance-none rounded-lg px-4 py-2 text-sm bg-white dark:bg-[#262728] border dark:text-white/50">
          <option value="">All Cities</option>
          <option v-for="city in cities" :key="city" :value="city">{{ city.replace("_", " ") }}</option>
        </select>
        <ChevronDown class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
      </div>

      <div class="relative w-full lg:w-1/4 dark:bg-[#262728]">
        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input v-model="searchDriver" type="text" placeholder="Search Driver" class="w-full background-light-blue rounded-lg px-4 py-2 pl-8 text-sm bg-white border dark:bg-[#262728]" />
      </div>
    </div>

    <div class="-top-8 left-6 p-4 rounded z-20 mt-3">
      <table class="w-full min-w-full">
        <thead class="border-b">
          <tr class="text-left text-gray-500">
            <th class="px-2 py-1 text-xs md:text-sm">Driver</th>
            <th class="px-2 py-1 text-xs md:text-sm">City</th>
            <th class="px-2 py-1 text-right text-xs md:text-sm"></th>
          </tr>
        </thead>

        <tbody>
          <template v-for="driver in paginatedDrivers" :key="driver.id">
            <tr class="border-b cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700" @click="toggleExpand(driver)">
              <td class="py-3 text-xs md:text-sm font-medium w-full max-w-[280px] px-2 truncate dark:text-white/70">
                {{ driver.name }}
              </td>
              <td class="px-2 py-1 text-xs md:text-sm">
                {{ driver.city }}
              </td>
              <td class="text-right px-2">
                <ChevronDown 
                  class="w-4 h-4 dark:text-blue-400 inline-block transition-transform duration-300" 
                  :class="{ 'rotate-180': expandedDriverId === driver.id }"
                />
              </td>
            </tr>

            <tr v-if="expandedDriverId === driver.id">
              <td colspan="3" class="px-6 py-6 bg-gray-50/50 dark:bg-black/20">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-y-4 gap-x-8">
                  <div 
                    v-for="doc in documentList" 
                    :key="doc" 
                    class="flex items-center gap-3 group transition-opacity"
                    :class="[
                      isDocDisabled(doc, driver) ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer',
                      !driverDocsData && driver.zohoId && isLoading ? 'opacity-50 cursor-wait' : ''
                    ]"
                    @click="(!isDocDisabled(doc, driver)) ? toggleDoc(doc) : null"
                  >
                    <div :class="isDocDisabled(doc, driver) ? 'text-gray-300' : 'text-blue-600'">
                      <CheckSquare v-if="selectedDocs.includes(doc)" :size="20" />
                      <Square v-else :size="20" class="text-gray-400 group-hover:text-blue-400" />
                    </div>

                    <div class="flex flex-col">
                      <span class="text-gray-700 dark:text-gray-300 font-medium text-xs md:text-sm">{{ doc }}</span>
                      
                      <span v-if="!driver.zohoId" class="text-[9px] text-orange-500 font-bold uppercase">
                        No Zoho ID
                      </span>
                      <span v-else-if="isLoading && expandedDriverId === driver.id && !driverDocsData" class="text-[9px] text-blue-400 animate-pulse">
                        Checking...
                      </span>
                      <span v-else-if="isDocDisabled(doc, driver)" class="text-[9px] text-gray font-bold uppercase">
                        N/A
                      </span>
                    </div>
                  </div>

                  <div class="md:col-start-3 flex items-center justify-end gap-2 mt-4">
                    <button 
                      v-if="driverDocsData"
                      @click.stop="selectAllAvailable"
                      class="text-[10px] text-blue-600 font-bold uppercase hover:underline"
                    >
                      {{ isAllSelected ? 'Uncheck All' : 'Select All Available' }}
                    </button>
                    
                    <button 
                      :disabled="selectedDocs.length === 0 || isLoading"
                      @click.stop.prevent="handleChecklistDownload(driver)"
                      class="px-8 py-2.5 transition-all rounded font-bold uppercase text-[10px] tracking-wider flex items-center justify-center"
                      :class="[
                        selectedDocs.length > 0 && !isLoading 
                          ? 'bg-blue-600 text-white hover:bg-blue-700 shadow-md cursor-pointer' 
                          : 'bg-gray-200 text-gray-400 cursor-not-allowed',
                        'dark:disabled:bg-gray-800 dark:disabled:text-gray-600'
                      ]"
                    >
                      <Loader2 v-if="isLoading" class="w-3 h-3 animate-spin mr-2" />
                      Download ({{ selectedDocs.length }})
                    </button>
                  </div>
                </div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>

      <div class="flex justify-between place-items-center mt-5">
        <div class="relative flex items-center">
          <select v-model="itemsPerPage" class="w-auto appearance-none rounded-lg px-4 py-2 text-sm bg-white dark:bg-[#262728] border dark:text-white/50">
            <option v-for="option in perPageOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
          </select>
          <ChevronDown class="w-4 h-4 text-gray-400 absolute right-3 pointer-events-none" />
        </div>

        <div v-if="filteredDrivers.length > itemsPerPage" class="flex items-center space-x-2">
          <button class="px-3 py-1 rounded border disabled:opacity-50" :disabled="currentPage === 1" @click="goToPage(currentPage - 1)">&lt;</button>
          <button v-for="page in visiblePages" :key="page" @click="goToPage(page)" class="px-3 py-1 rounded border transition-colors" :class="currentPage === page ? 'bg-[#0078d4] text-white' : 'text-blue-500 border-gray-200'">{{ page }}</button>
          <button class="px-3 py-1 rounded border disabled:opacity-50" :disabled="currentPage === totalPages" @click="goToPage(currentPage + 1)">&gt;</button>
        </div>
      </div>
    </div>
  </div>
</template>