<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
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
import CERTN from '@/components/cards/CERTN.vue';
import { ChevronDown } from 'lucide-vue-next';

definePageMeta({
  middleware: ['auth']
})

const { user, isLoggedIn, me } = useAuth();
const { fetchZohoDetails, driverDetails, isLoading } = useZoho();
const activeTab = ref('personal');

const tabs = [
  { id: 'personal', label: 'Personal' },
  { id: 'vehicle', label: 'Vehicle' },
  { id: 'documents', label: 'Documents' },
  { id: 'licensing', label: 'Licensing' }
];

//watch(isLoggedIn, (isNowLoggedIn) => {
  //if (isNowLoggedIn && user.value?.zoho_id) {
  //  fetchZohoDetails();
 // }
//}, { immediate: true });

// Fetch when the component mounts
onMounted(async () => {
  if (!isLoggedIn.value) {
    await me();
  } 
  if (user.value?.zoho_id && !driverDetails.value && !isLoading.value) {
    fetchZohoDetails();
  }
});

watch(() => user.value?.zoho_id, (newId) => {
  if (newId && !driverDetails.value && !isLoading.value) {
    fetchZohoDetails();
  }
});
</script>

<template>
  <div class="flex flex-col gap-y-12 p-8 min-h-screen">
    <div class="flex justify-center w-full">
      <div class="w-full flex justify-center">
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
    </div>

    <div class="">
      <div v-if="activeTab === 'personal'" class="grid grid-cols-1 lg:grid-cols-2 gap-y-4 justify-items-center">
        <Banking
        v-if="driverDetails"
        :details="driverDetails" 
        :loading="isLoading"
        />

        <HSTGST
        v-if="driverDetails"
        :details="driverDetails" 
        :loading="isLoading"
        />

        <div 
          v-if="isLoading" 
          class="fixed inset-0 flex flex-col items-center justify-center bg-black/70 backdrop-blur-sm z-50"
        >
          <div class="relative">
            <div class="w-20 h-20 rounded-full border-4 border-t-blue-500 border-r-blue-700 border-b-blue-900 border-l-blue-900 animate-spin"></div>
            
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-blue-500 font-bold text-xs">LOADING</span>
            </div>
          </div>
          
          <span class="mt-4 text-white text-sm font-medium tracking-widest animate-pulse">
            Connecting to Zoho...
          </span>
        </div>
      </div>

      <div v-if="activeTab === 'vehicle'" class="grid grid-cols-1 lg:grid-cols-2 gap-y-4 justify-items-center">
        <CarPhoto 
        :details="driverDetails" 
        :loading="isLoading"
        />
        <InsurancePolicy 
        :details="driverDetails" 
        :loading="isLoading"
        />
        <Ownership 
        :details="driverDetails" 
        :loading="isLoading"
        />
        <VehicleSafety 
        :details="driverDetails" 
        :loading="isLoading"
        />
      </div>

      <div v-if="activeTab === 'documents'" class="grid grid-cols-1 lg:grid-cols-2 gap-y-4 justify-items-center">
        <DriversAbstract 
        :details="driverDetails" 
        :loading="isLoading"
        />
        <CERTN 
        :details="driverDetails" 
        :loading="isLoading"
        />
      </div>

      <div v-if="activeTab === 'licensing'" class="grid grid-cols-1 lg:grid-cols-2 gap-y-4 justify-items-center">
        <DriversLicense 
        :details="driverDetails" 
        :loading="isLoading"
        />
        <CityLicense 
        :details="driverDetails" 
        :loading="isLoading"
        />
      </div>
    </div>

    <div class="">

      
    </div>

  </div>
</template>