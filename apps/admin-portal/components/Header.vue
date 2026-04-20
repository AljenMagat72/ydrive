<script setup lang="ts">
import {
  Bell,
  BellDot,
  BellIcon,
  Clock,
  CogIcon,
  HomeIcon,
  InfoIcon,
  LogOut,
  Moon,
  Search,
  Sun,
  User,
  X,
  FileText
} from "lucide-vue-next";
import { ref, computed } from "vue";
import { fetchAllDrivers } from "~/lib/api/drivers";
import { useZoho } from "#imports";

const { fetchZohoDetails } = useZoho();
const drivers = useState<any[]>("drivers", () => []);
const isProcessingSync = ref(false);
const authToken = useAuthToken();

const syncZohoDetails = async () => {
  if (isProcessingSync.value || !drivers.value.length) return;
  
  isProcessingSync.value = true;

  try {
    for (const driver of drivers.value) {
      /** * THE KILL SWITCH:
       * If drivers are cleared or user logged out while this loop is running,
       * exit the function immediately so no more API calls fire.
       */
      if (drivers.value.length === 0 || !authToken.value) {
        console.log("ync aborted: User logged out or drivers cleared.");
        return; 
      }

      const zId = driver.zodo_id || driver.zoho_id || driver.zohoId;

      if (zId && !driver.zoho_docs && !driver.isFetching) {
        try {
          driver.isFetching = true; 
          const data = await fetchZohoDetails(zId);
          
          if (data) {
            driver.zoho_docs = data;
            driver.License_Exp      = data.License_Exp;
            driver.Insurance_Exp    = data.Insurance_Exp;
            driver.Registration_Exp = data.Registration_Exp;
            driver.Safety_Exp       = data.Safety_Exp;
            driver.City_License_Exp = data.City_License_Exp;
            driver.Abstract_Exp     = data.Abstract_Exp;
            driver.Criminal_Exp     = data.Criminal_Exp;
          }
        } catch (error) {
          console.warn(`Sync failed for ${zId}:`, error);
        } finally {
          driver.isFetching = false;
        }
      }
    }
  } finally {
    isProcessingSync.value = false;
  }
};

watch(drivers, (newVal) => {
  if (newVal?.length > 0 && !isProcessingSync.value) {
    syncZohoDetails();
  }
}, { deep: true });

const colorMode = useColorMode();

definePageMeta({
  middleware: "auth",
});

const auth = useAuth();
const isOpen = ref(false);
const mobileMenu = ref(false);
const openMenu = ref<string | null>(null);
const menuButtonRef = ref<HTMLElement | null>(null);
const menuRef = ref<HTMLElement | null>(null);
const theme = ref("light");

const userName = useUserName();
const userRole = useUserRole();
const activePage = useCurrentPage();

const handleClickOutside = (event: MouseEvent) => {
  if (
    menuRef.value &&
    !menuRef.value.contains(event.target as Node) &&
    menuButtonRef.value &&
    !menuButtonRef.value.contains(event.target as Node)
  ) {
    mobileMenu.value = false;
    openMenu.value = null;
  }
};

onMounted(() => {
  document.addEventListener("click", handleClickOutside);
  syncZohoDetails();
});

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside);
});

const toggleMenu = (menu: string) => {
  openMenu.value = openMenu.value === menu ? null : menu;
};

const handleLogout = () => {
  drivers.value = [];
  auth.logout();
  navigateTo("/login");
};

const handleAlert = () => alert("Sorry, this feature is under development.");

const toggleTheme = () => {
  colorMode.preference = colorMode.preference === "dark" ? "light" : "dark";
};

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
</script>

<template>
  <div
    class="h-full flex lg:flex-row-reverse md:flex-row-reverse sm:flex-row-reverse xl:flex-row-reverse items-center px-6 lg:py-6 py-3 justify-between shadow lg:shadow-none dark:bg-[#262728]"
  >
    <!-- Desktop Menu -->
    <div class="flex justify-between w-full">
      <div class="hidden md:flex-1 md:flex justify-center items-center">
        <div class="flex items-center rounded-xl shadow-blue gap-x-2">
          <button
            class="cursor-pointer hover:hvr-color-blue font-bold px-4 py-2 md:px-6 md:py-3 text-sm md:text-xs lg:text-base"
          >
            Dispatch
          </button>

          <span class="text-blue-300">|</span>

          <NuxtLink
            to="/driver/schedule"
            class="cursor-pointer hover:hvr-color-blue font-bold px-4 py-2 md:px-6 md:py-3 text-sm md:text-xs lg:text-base"
          >
            Admin
          </NuxtLink>

          <span class="text-blue-300">|</span>

          <span
            class="cursor-pointer hover:hvr-color-blue font-bold px-4 py-2 md:px-6 md:py-3 text-sm md:text-xs whitespace-nowrap lg:text-base"
          >
            Help Desk
          </span>

          <span class="text-blue-300">|</span>

          <span class="flex items-center gap-x-2 px-2">
            <input
              v-show="isOpen"
              type="text"
              placeholder="Search..."
              class="px-2 py-1 w-0 opacity-0 transition-all duration-300 text-sm md:text-base focus:w-32 md:focus:w-40 focus:opacity-100"
              :class="isOpen ? 'w-32 md:w-40 opacity-100' : 'w-0 opacity-0'"
            />
            <Search @click="isOpen = !isOpen" />
          </span>
        </div>
      </div>

      <!-- Right Profile (desktop only) -->
      <div
        class="hidden md:flex items-center lg:gap-x-6 md:gap-x-3 ml-auto dark:text-white/80"
      >
        <Moon v-if="colorMode.preference == 'light'" @click="toggleTheme" />

        <Sun v-else="colorMode.preference == 'dark'" @click="toggleTheme" />

        <BellIcon />

        <User />
        <div class="flex flex-col gap-y-1">
          <span class="font-medium block">{{ userName }} </span>
          <span class="text-xs text-gray-500">{{
            userRole?.toUpperCase()
          }}</span>
        </div>
      </div>

      <!-- MOBILE HAMBURGER -->
      <button
        @click="mobileMenu = !mobileMenu"
        ref="menuButtonRef"
        class="md:hidden flex items-center"
      >
        <svg
          class="w-8 h-8 text-gray-700 dark:text-[#bdbfc2]"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M4 6h16M4 12h16M4 18h16"
          />
        </svg>
      </button>
    </div>

    <!-- Right: Logo -->
    <NuxtLink
      to="/admin/dashboard"
      class="flex items-center pr-6"
      style="cursor: pointer"
    >
      <img
        src="/assets/logos/y-drive-logo.png"
        alt="YDrive"
        class="h-12 ml-4"
      />
    </NuxtLink>
  </div>

  <!-- MOBILE DROPDOWN MENU -->
  <transition
    name="slide-fade-left-to-right"
    enter-active-class="transition transform duration-500 ease-out"
    leave-active-class="transition transform duration-500 ease-in"
    enter-from-class="opacity-0 -translate-x-full"
    enter-to-class="opacity-100 translate-x-0"
    leave-from-class="opacity-100 translate-x-0"
    leave-to-class="opacity-0 -translate-x-full"
  >
    <div
      ref="menuRef"
      v-if="mobileMenu"
      class="md:hidden absolute w-72 pb-4 bg-white shadow-blue dark:shadow-lg flex flex-col gap-6 animate-fade z-50 h-screen dark:bg-[#262728]"
    >
      <div class="flex justify-between place-items-center gap-4 pt-10 px-4">
        <div class="flex place-items-center">
          <img
            src="/assets/images/admin-avatar.png"
            class="cursor-pointer h-14"
          />
          <div class="ml-3 flex flex-col justify-center">
            <h4 class="text-lg font-semibold leading-tight dark:text-white/70">
              Hi {{ userName?.split(" ")[0] }}
            </h4>
            <small class="text-gray-500 text-xs leading-snug">
              YDRIVE {{ userRole?.toUpperCase() }}
            </small>
          </div>
        </div>
        <X @click="mobileMenu = !mobileMenu" class="text-gray-400" />
      </div>

      <hr />

      <div class="px-5">
        <h6 class="text-xs text-gray-400 mb-2">Theme</h6>
        <div
          class="flex w-full p-2 bg-gray-100 rounded-lg transition-colors duration-300 dark:bg-[#1C1C1D]"
        >
          <button
            class="w-full p-1 flex justify-center items-center gap-1 transition-all duration-700 ease-in-out"
            :class="
              colorMode.preference === 'light'
                ? 'border rounded shadow-lg bg-white text-black scale-105'
                : 'text-gray-400'
            "
            @click="toggleTheme"
          >
            <Sun :size="18" />
            Light
          </button>

          <button
            class="w-full p-1 flex justify-center items-center gap-1 transition-all duration-600 ease-in-out"
            :class="
              colorMode.preference === 'dark'
                ? 'border rounded shadow-lg bg-gray-700 text-white scale-105'
                : 'text-gray-400'
            "
            @click="toggleTheme"
          >
            <Moon :size="18" />
            Dark
          </button>
        </div>
      </div>

      <NuxtLink
        to="/admin/dashboard"
        class="flex items-center cursor-pointer px-5 md:px-6 py-2 gap-x-3 font-bold text-base md:text-lg leading-5 color-blue rounded hover:bg-ydrive-blue hover:text-white mt-5 dark:text-white/70"
      >
        <HomeIcon :size="18" />
        Home
      </NuxtLink>

      <div
        @click="toggleMenu('drivers')"
        class="relative flex items-center cursor-pointer px-5 md:px-6 py-2 gap-x-3 font-bold text-base md:text-lg leading-5 color-blue rounded hover:bg-ydrive-blue hover:text-white dark:text-white/70"
      >
        Drivers
        
        <span 
          v-if="hasExpiredDocs || hasSoonDocs" 
          :class="[
            'absolute right-4 top-3 h-2.5 w-2.5 rounded-full border border-white',
            hasExpiredDocs ? 'bg-red-500 animate-pulse' : 'bg-amber-500'
          ]"
        ></span>
      </div>

      <!-- Drivers Submenu -->
      <div v-if="openMenu === 'drivers'" class="flex flex-col">
        <NuxtLink
          to="/driver/schedule"
          class="flex items-center pl-12 py-2.5 cursor-pointer font-semibold text-sm md:text-lg leading-5 color-blue rounded hover:bg-ydrive-blue hover:text-white dark:text-white/70"
        >
          <Clock :size="18" class="mr-2" />
          Schedule
        </NuxtLink>
      </div>
      <div v-if="openMenu === 'drivers'" class="flex flex-col">
        <NuxtLink
          to="/driver/documents"
          class="flex items-center pl-12 py-2.5 cursor-pointer font-semibold text-sm md:text-lg leading-5 color-blue rounded hover:bg-ydrive-blue hover:text-white dark:text-white/70"
        >
          <component 
            :is="hasExpiredDocs || hasSoonDocs ? BellDot : FileText" 
            :size="18" 
            :class="[
              'mr-2',
              hasExpiredDocs ? 'text-red-500' : hasSoonDocs ? 'text-amber-500' : ''
            ]" 
          />
          <span :class="{
            'text-red-600': hasExpiredDocs,
            'text-amber-600': !hasExpiredDocs && hasSoonDocs
          }">
            Documents
          </span>
        </NuxtLink>
      </div>

      <hr class="mt-auto" />
      <div
        @click="handleLogout"
        class="flex items-center cursor-pointer px-5 md:px-6 py-2 gap-x-3 font-bold text-base md:text-lg leading-5 color-blue rounded dark:text-white/70"
      >
        <LogOut :size="18" />
        Logout
      </div>
    </div>
  </transition>
</template>

<style>
body {
  background-color: #fff;
  color: rgba(0, 0, 0, 0.8);
}
.dark body {
  background-color: #091a28;
  color: #ebf4f1;
}
.sepia body {
  background-color: #f1e7d0;
  color: #433422;
}
</style>
