<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue';
import { useAuth, useZoho, definePageMeta } from '#imports'
import DriversLicense from '@/components/cards/DriversLicense.vue';
import CityLicense from '@/components/cards/CityLicense.vue';
import Banking from '@/components/cards/Banking.vue';
import HSTGST from '@/components/cards/HSTGST.vue';
import CarPhoto from '@/components/cards/CarPhoto.vue';
import InsurancePolicy from '@/components/cards/InsurancePolicy.vue';
import Ownership from '@/components/cards/Ownership.vue';
import VehicleSafety from '@/components/cards/VehicleSafety.vue';
import DriversAbstract from '@/components/cards/DriversAbstract.vue';
import CriminalCheck from '@/components/cards/CriminalCheck.vue';
import { ChevronDown } from 'lucide-vue-next';

definePageMeta({
  middleware: ['auth']
})

const { user, isLoggedIn, me } = useAuth();
const { fetchZohoDetails, driverDetails, isLoading } = useZoho();
const driverCity = computed(() => driverDetails.value?.City?.trim() || '');
const activeTab = ref('personal');

const showCriminalCheck = computed(() => {
  const restrictedCities = ['Lindsay', 'Cobourg', 'Medicine Hat', 'Lethbridge'];
  return !restrictedCities.includes(driverCity.value);
});

const showVehicleSafety = computed(() => {
  const restrictedCities = ['Grande Prairie', 'Medicine Hat', 'Lethbridge'];
  return !restrictedCities.includes(driverCity.value);
});

const showCityLicense = computed(() => {
  const restrictedCities = ['Medicine Hat', 'Lethbridge'];
  return !restrictedCities.includes(driverCity.value);
});

const tabs = [
  { id: 'personal', label: 'Personal' },
  { id: 'vehicle', label: 'Vehicle' },
  { id: 'documents', label: 'Documents' },
  { id: 'licensing', label: 'Licensing' }
];

onMounted(async () => {
  if (!isLoggedIn.value) {
    await me();
  } 

  const zohoId = user.value?.zoho_id;
  if (zohoId && !driverDetails.value && !isLoading.value) {
    await fetchZohoDetails();
  }
});

watch(() => user.value?.zoho_id, (newId) => {
  if (newId && !driverDetails.value && !isLoading.value) {
    fetchZohoDetails();
  }
});

const vehicleStatus = computed(() => {
  if (!driverDetails.value) return 'clear';
  
  const today = new Date();
  today.setHours(0, 0, 0, 0);

  const thirtyDaysFromNow = new Date();
  thirtyDaysFromNow.setDate(today.getDate() + 30);
  const vehicleKeys = [
    'Insurance_Exp',
    'Registration_Exp',
    'Safety_Exp'
  ];

  let status = 'clear';

  for (const key of vehicleKeys) {
    const dateValue = driverDetails.value[key];
    if (!dateValue || dateValue === '---') continue;

    const expiryDate = new Date(dateValue);
    
    if (expiryDate < today) {
      return 'expired';
    } 
    
    if (expiryDate <= thirtyDaysFromNow) {
      status = 'warning';
    }
  }

  return status;
});

const documentsStatus = computed(() => {
  if (!driverDetails.value) return 'clear';
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  const thirtyDays = new Date(today.getTime() + 30 * 24 * 60 * 60 * 1000);

  const keys = ['Criminal_Check_Exp', 'Abstract_Exp'];
  let status = 'clear';

  for (const key of keys) {
    const val = driverDetails.value[key];
    if (!val || val === '---') continue;
    const expiry = new Date(val);
    if (expiry < today) return 'expired';
    if (expiry <= thirtyDays) status = 'warning';
  }
  return status;
});

const licensingStatus = computed(() => {
  if (!driverDetails.value) return 'clear';
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  const thirtyDays = new Date(today.getTime() + 30 * 24 * 60 * 60 * 1000);

  const keys = ['License_Exp', 'City_License_Exp'];
  let status = 'clear';

  for (const key of keys) {
    const val = driverDetails.value[key];
    if (!val || val === '---') continue;
    const expiry = new Date(val);
    if (expiry < today) return 'expired';
    if (expiry <= thirtyDays) status = 'warning';
  }
  return status;
});

const getTabStatus = (tabId: string) => {
  if (tabId === 'vehicle') return vehicleStatus.value;
  if (tabId === 'documents') return documentsStatus.value;
  if (tabId === 'licensing') return licensingStatus.value;
  return 'clear';
};

const globalExpiryStatus = computed(() => {
  // Check all statuses. If any are 'expired', the whole UI turns red.
  if (
    vehicleStatus.value === 'expired' || 
    documentsStatus.value === 'expired' || 
    licensingStatus.value === 'expired'
  ) {
    return 'expired';
  }
  
  // If none are expired but some are warning, turn amber.
  if (
    vehicleStatus.value === 'warning' || 
    documentsStatus.value === 'warning' || 
    licensingStatus.value === 'warning'
  ) {
    return 'warning';
  }

  return 'clear';
});

const isDropdownOpen = ref(false);
const toggleDropdown = () => (isDropdownOpen.value = !isDropdownOpen.value);

const selectTab = (tabId: string) => {
  activeTab.value = tabId;
  isDropdownOpen.value = false;
};

</script>

<template>
  <div class="flex flex-col gap-y-12 p-8 min-h-screen">
    <div class="flex justify-center w-full">
      <div class="sm:hidden w-full px-4 flex flex-col gap-3">
        <div 
          v-if="globalExpiryStatus !== 'clear'" 
          class="flex items-center gap-3 p-3 rounded-xl border animate-pulse shadow-lg"
          :class="globalExpiryStatus === 'expired' ? 'bg-red-500/10 border-red-500 text-red-500' : 'bg-amber-500/10 border-amber-500 text-amber-500'"
        >
          <AlertCircle :size="16" :stroke-width="3" />
          <span class="text-[10px] font-black uppercase tracking-widest">
            {{ globalExpiryStatus === 'expired' ? 'Action Required: Documents Expired' : 'Notice: Upcoming Expiries' }}
          </span>
        </div>

        <div class="relative w-full">
        <button 
          @click="toggleDropdown"
          class="relative z-10 w-full flex items-center justify-between dark:bg-black bg-zinc-900 border rounded-xl px-4 py-4 transition-all duration-500 font-bold text-lg text-left"
          :class="[
            globalExpiryStatus === 'expired' ? 'border-red-500 shadow-[0_0_20px_rgba(239,68,68,0.4)] text-red-500' : 
            globalExpiryStatus === 'warning' ? 'border-amber-500 shadow-[0_0_20px_rgba(245,158,11,0.3)] text-amber-500' : 
            'border-blue-500 shadow-blue text-white'
          ]"
        >
          <div class="flex items-center gap-2">
            <div 
              v-if="getTabStatus(activeTab) !== 'clear'"
              class="size-2 rounded-full animate-pulse"
              :class="getTabStatus(activeTab) === 'expired' ? 'bg-red-500 shadow-[0_0_8px_#ef4444]' : 'bg-amber-500 shadow-[0_0_8px_#f59e0b]'"
            ></div>
            {{ tabs.find(t => t.id === activeTab)?.label }}
          </div>
          <ChevronDown class="w-6 h-6 transition-transform" :class="{ 'rotate-180': isDropdownOpen }" />
        </button>

        <div 
          v-if="isDropdownOpen" 
          class="absolute top-[110%] left-0 w-full z-[100] bg-zinc-950 border border-zinc-800 rounded-xl overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,1)]"
        >
          <div 
            v-for="tab in tabs" 
            :key="tab.id"
            @click="selectTab(tab.id)"
            class="flex items-center justify-between px-5 py-5 border-b border-zinc-900 last:border-none active:bg-blue-600/20 transition-colors"
          >
            <span :class="activeTab === tab.id ? 'text-blue-400 font-black' : 'text-zinc-200 font-semibold'">
              {{ tab.label }}
            </span>
            
            <div v-if="getTabStatus(tab.id) !== 'clear'" class="flex items-center gap-2">
              <span class="text-[10px] font-black uppercase" :class="getTabStatus(tab.id) === 'expired' ? 'text-red-500' : 'text-amber-500'">
                {{ getTabStatus(tab.id) === 'expired' ? 'EXPIRED' : 'SOON' }}
              </span>
              <div 
                class="size-2.5 rounded-full animate-pulse"
                :class="getTabStatus(tab.id) === 'expired' ? 'bg-red-500 shadow-[0_0_8px_#ef4444]' : 'bg-amber-500 shadow-[0_0_8px_#f59e0b]'"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="hidden sm:inline-flex dark:bg-black border border-blue-500 rounded-full shadow-blue overflow-hidden">
      <button 
        v-for="tab in tabs" 
        :key="tab.id"
        @click="activeTab = tab.id"
        class="relative px-8 py-2 text-lg font-semibold transition-all duration-200"
        :class="activeTab === tab.id ? 'bg-blue-600 text-white' : 'dark:text-white hover:text-white hover:bg-blue-600'"
      >
        {{ tab.label }}

        <span 
          v-if="getTabStatus(tab.id) !== 'clear'"
          class="absolute top-2 right-4 w-2.5 h-2.5 rounded-full animate-pulse border border-white dark:border-black"
          :class="getTabStatus(tab.id) === 'expired' 
            ? 'bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.8)]' 
            : 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.8)]'"
        ></span>
      </button>
    </div>
  </div>

    <div class="relative min-h-[400px]">
      <div v-if="isLoading" class="absolute inset-0 flex flex-col items-center justify-center bg-black/70 backdrop-blur-sm z-50 rounded-2xl">
        <div class="relative">
          <div class="w-20 h-20 rounded-full border-4 border-t-blue-500 border-r-blue-700 border-b-blue-900 border-l-blue-900 animate-spin"></div>
          <div class="absolute inset-0 flex items-center justify-center">
            <span class="text-blue-500 font-bold text-[10px]">LOADING</span>
          </div>
        </div>
        <span class="mt-4 text-white text-sm font-medium tracking-widest animate-pulse">
          Syncing with Zoho...
        </span>
      </div>

      <div v-if="driverDetails">
        <div v-if="activeTab === 'personal'" class="grid grid-cols-1 lg:grid-cols-2 gap-8 justify-center mx-auto max-w-fit">
          <Banking :details="driverDetails" />
          <HSTGST :details="driverDetails" />
        </div>

        <div v-if="activeTab === 'vehicle'" class="grid grid-cols-1 lg:grid-cols-2 gap-8 justify-center mx-auto max-w-fit">
          <CarPhoto :details="driverDetails" />
          <InsurancePolicy :details="driverDetails" />
          <Ownership :details="driverDetails" />
          <VehicleSafety v-if="showVehicleSafety" :details="driverDetails" />
        </div>

        <div v-if="activeTab === 'documents'" class="flex flex-wrap gap-8 justify-center mx-auto max-w-fit">
          <DriversAbstract :details="driverDetails" />
          <CriminalCheck v-if="showCriminalCheck" :details="driverDetails" />
        </div>

        <div v-if="activeTab === 'licensing'" class="flex flex-wrap gap-8 justify-center mx-auto max-w-fit">
          <DriversLicense :details="driverDetails" />
          <CityLicense v-if="showCityLicense" :details="driverDetails" />
        </div>
      </div>
    </div>
  </div>
</template>