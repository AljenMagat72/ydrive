<script setup lang="ts">
import {
  Ban,
  Car,
  Hand,
  Handshake,
  HandshakeIcon,
  Home,
  BellDot,
  FileText,
  Clock,
  LogOut,
} from "lucide-vue-next";
import { ref } from "vue";

const auth = useAuth();

const drivers = useState<any[]>("drivers");

const isExpired = (dateString: string | null) => {
  if (!dateString) return false;
  const expiryDate = new Date(dateString);
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  return expiryDate < today;
};

const isExpiringSoon = (dateString: string | null) => {
  if (!dateString) return false;
  const expiryDate = new Date(dateString);
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  const thirtyDaysFromNow = new Date();
  thirtyDaysFromNow.setDate(today.getDate() + 30);
  return expiryDate >= today && expiryDate <= thirtyDaysFromNow;
};

const hasExpiredDocs = computed(() => {
  return drivers.value.some(driver => 
    [driver.License_Exp, driver.Insurance_Exp, driver.Registration_Exp, driver.Safety_Exp, driver.City_License_Exp, driver.Abstract_Exp, driver.Criminal_Exp]
    .some(isExpired)
  );
});

const hasSoonDocs = computed(() => {
  return drivers.value.some(driver => 
    [driver.License_Exp, driver.Insurance_Exp, driver.Registration_Exp, driver.Safety_Exp, driver.City_License_Exp, driver.Abstract_Exp, driver.Criminal_Exp]
    .some(isExpiringSoon)
  );
});

const handleLogout = () => {
  drivers.value = [];
  auth.logout();
  navigateTo("/login");
};

const openMenu = ref<string | null>("");

const toggleMenu = (menu: string) => {
  openMenu.value = openMenu.value === menu ? null : menu;
};
</script>

<template>
  <!-- Sidebar -->
  <div
    class="flex flex-col h-[100vh] overflow-auto bg-white w-full md:w-[15%] flex-none transition-all duration-300 md:flex dark:bg-[#262728] px-6 py-6"
  >
    <!-- SIDEBAR ITEMS -->
    <div class="flex flex-col space-y-3 mt-7">
      <!-- Home -->
      <NuxtLink
        to="/admin/dashboard"
        class="flex items-center cursor-pointer px-4 py-2 gap-x-2 font-bold text-base md:text-lg leading-5 color-blue rounded hover:bg-ydrive-blue hover:text-white dark:text-white/80"
      >
        <Home :size="18" />
        Home
      </NuxtLink>

      <!-- Drivers -->
      <div
        @click="toggleMenu('drivers')"
        class="relative flex items-center cursor-pointer px-4 py-2 gap-x-2 font-bold text-base text-lg leading-5 color-blue rounded hover:bg-ydrive-blue hover:text-white dark:text-white/80"
      >
        <svg viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
           <path
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12ZM10.7627 19.9049C10.1039 18.2879 8.22077 13.9069 7 13.5C6.14617 13.2154 4.96833 13.2548 4.112 13.3416C4.68235 16.7196 7.37002 19.3781 10.7627 19.9049ZM4.32945 9.72049C5.31094 6.41274 8.37371 4 12 4C15.6263 4 18.6891 6.41274 19.6706 9.72049C18.0917 9.42464 15.2582 9 12 9C8.74181 9 5.90825 9.42464 4.32945 9.72049ZM19.888 13.3416C19.0317 13.2548 17.8538 13.2154 17 13.5C15.7792 13.9069 13.8961 18.2879 13.2373 19.9049C16.63 19.3781 19.3176 16.7196 19.888 13.3416Z"
          />
        </svg>
        Drivers

        <span 
          v-if="hasExpiredDocs || hasSoonDocs" 
          :class="[
            'absolute right-4 top-4 h-2.5 w-2.5 rounded-full border border-white',
            hasExpiredDocs ? 'bg-red-500 animate-pulse' : 'bg-amber-500'
          ]"
        ></span>
      </div>

      <!-- Drivers Submenu -->
      <div v-if="openMenu === 'drivers'" class="flex flex-col">
        <NuxtLink
          to="/driver/schedule"
          class="flex items-center pl-12 py-2.5 cursor-pointer font-semibold text-sm text-lg leading-5 color-blue rounded hover:bg-ydrive-blue hover:text-white dark:text-white/80"
        >
        <Clock :size="18" class="mr-2" />
          Schedule
        </NuxtLink>
      </div>

      <div v-if="openMenu === 'drivers'" class="flex flex-col">
        <NuxtLink
          to="/driver/documents"
          class="flex items-center pl-12 py-2.5 cursor-pointer font-semibold text-sm text-lg leading-5 color-blue rounded hover:bg-ydrive-blue hover:text-white dark:text-white/80"
        >
          <component 
            :is="hasExpiredDocs || hasSoonDocs ? BellDot : FileText" 
            :size="16" 
            :class="['mr-2', hasExpiredDocs ? 'text-red-500' : hasSoonDocs ? 'text-amber-500' : '']" 
          />
          <span :class="{'text-red-600': hasExpiredDocs, 'text-amber-600': !hasExpiredDocs && hasSoonDocs}">
            Documents
          </span>
        </NuxtLink>
      </div>

      <div v-if="openMenu === 'drivers'" class="flex flex-col hidden">
        <NuxtLink
          to="/driver/no-opps"
          class="flex items-center pl-15 py-2.5 cursor-pointer font-semibold text-sm text-lg leading-5 color-blue rounded hover:bg-ydrive-blue hover:text-white dark:text-white/80"
        >
          No Opps
        </NuxtLink>
      </div>

      <NuxtLink
        to="/vendor/vendors"
        class="flex hidden items-center cursor-pointer px-4 py-2 gap-x-2 font-bold text-base md:text-lg leading-5 color-blue rounded hover:bg-ydrive-blue hover:text-white dark:text-white/80"
      >
        <HandshakeIcon />
        Vendors
      </NuxtLink>
    </div>

    <!-- Logout -->
    <div
      @click="handleLogout"
      class="flex items-center cursor-pointer px-5 py-2 gap-x-2 font-bold text-base text-lg leading-5 color-blue rounded hover:bg-ydrive-blue hover:text-white dark:text-white/80 mt-2"
    >
      <LogOut :size="18" />
      Logout
    </div>
  </div>
</template>
