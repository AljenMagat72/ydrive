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
</script>

<template>
  <div class="text-white bg-gray-800 p-2 text-xs rounded mb-4">
  Current City: "{{ driverCity }}" | 
  Hide Safety? {{ !showVehicleSafety }}
</div>
  <div class="flex flex-col gap-y-12 p-8 min-h-screen">
    <div class="flex justify-center w-full">
      <div class="sm:hidden w-full px-4">
        <div class="relative w-full">
          <select 
            v-model="activeTab"
            class="w-full bg-black border border-blue-500 text-white rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none shadow-blue"
          >
            <option v-for="tab in tabs" :key="tab.id" :value="tab.id">
              {{ tab.label }}
            </option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-blue-500">
            <ChevronDown class="w-5 h-5" />
          </div>
        </div>
      </div>

      <div class="hidden sm:inline-flex bg-black border border-blue-500 rounded-full shadow-blue overflow-hidden">
        <button 
          v-for="tab in tabs" 
          :key="tab.id"
          @click="activeTab = tab.id"
          class="px-8 py-2 text-lg font-semibold transition-all duration-200"
          :class="activeTab === tab.id ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white'"
        >
          {{ tab.label }}
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