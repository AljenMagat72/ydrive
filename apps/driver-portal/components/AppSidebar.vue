<script setup lang="ts">
import { Calendar, CalendarClock, CalendarPlus, ChevronRight, LogOut, User, Moon, 
  Sun } from 'lucide-vue-next';
import { useAuth, useRuntimeConfig, useColorMode, onMounted } from '#imports';
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarMenuItem,
  SidebarMenuButton,
  SidebarGroup,
  SidebarGroupContent,
  SidebarMenu,
  SidebarHeader,
  SidebarMenuSub,
  SidebarMenuSubItem,
  useSidebar,

} from '@/components/ui/sidebar';
import { Collapsible, CollapsibleTrigger, CollapsibleContent, } from './ui/collapsible';
import { Avatar, AvatarImage, AvatarFallback } from './ui/avatar';
import { computed } from 'vue';
import { useZoho } from '@/composables/use-zoho';
import { AlertCircle } from 'lucide-vue-next';

const { user, logout } = useAuth();
const { setOpenMobile } = useSidebar();
const config = useRuntimeConfig();
const showPersonal = computed(() => config.public.showZohoDocs);
const { driverDetails, fetchZohoDetails } = useZoho();

onMounted(() => {
  fetchZohoDetails();
});

const documentStatus = computed(() => {
  if (!driverDetails.value) return 'clear';
  
  const today = new Date();
  today.setHours(0, 0, 0, 0);

  const thirtyDaysFromNow = new Date();
  thirtyDaysFromNow.setDate(today.getDate() + 30);

  const expiryKeys = [
    'License_Exp', 
    'City_License_Exp', 
    'Criminal_Check_Exp', 
    'Abstract_Exp', 
    'Insurance_Exp', 
    'Registration_Exp', 
    'Safety_Exp'
  ];

  let status = 'clear';

  for (const key of expiryKeys) {
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

function closeSideBar() {
  setOpenMobile(false);
}

const colorMode = useColorMode();

const setTheme = (theme: 'light' | 'dark') => {
  colorMode.preference = theme;
};
</script>

<template>
  <Sidebar>
    <SidebarHeader>
    <div class="flex flex-col gap-y-4">
      <div class="flex flex-row items-center gap-2">
        <Avatar class="size-10">
          <AvatarImage :src="user?.avatar ?? ''" />
          <AvatarFallback>{{ user?.firstName[0] }}{{ user?.lastName[0] }}</AvatarFallback>
        </Avatar>
        <div class="text-xs">
          <p>{{ user?.firstName }}</p>
          <p>{{ user?.lastName }}</p>
        </div>
      </div>
      <div class="flex w-full p-1 rounded-lg transition-colors duration-300 dark:bg-[#1C1C1D] border dark:border-white/5 shadow-blue">
        <button
          type="button"
          class="w-full py-1.5 flex justify-center items-center gap-2 text-xs transition-all duration-200"
          :class="colorMode.preference === 'light' 
            ? 'bg-white rounded shadow-sm text-black font-semibold' 
            : 'text-gray-400 hover:text-blue-400'"
          @click="setTheme('light')"
        >
          <Sun :size="14" /> Light
        </button>

        <button
          type="button"
          class="w-full py-1.5 flex justify-center items-center gap-2 text-xs transition-all duration-200"
          :class="colorMode.preference === 'dark' 
            ? 'bg-gray-700 rounded shadow-sm text-white font-semibold' 
            : 'text-gray-400 hover:text-blue-400'"
          @click="setTheme('dark')"
        >
          <Moon :size="14" /> Dark
        </button>
      </div>
      </div>
    </SidebarHeader>
    <SidebarContent>
      <SidebarGroup>
        <SidebarGroupContent>
          <SidebarMenu>
            <Collapsible class="group/collapsible">
              <SidebarMenuItem>
                <CollapsibleTrigger as-child>
                  <SidebarMenuButton>
                    <Calendar /> Schedule
                    <ChevronRight
                      class="ml-auto transition-transform ml-auto group-data-[state=open]/collapsible:rotate-90"
                    />
                  </SidebarMenuButton>
                </CollapsibleTrigger>
                <CollapsibleContent>
                  <SidebarMenuSub>
                    <SidebarMenuSubItem>
                      <SidebarMenuButton as-child>
                        <NuxtLink
                          to="/schedule/current"
                          @click="closeSideBar"
                        >
                          <CalendarClock /> Current
                        </NuxtLink>
                      </SidebarMenuButton>
                    </SidebarMenuSubItem>
                    <SidebarMenuSubItem>
                      <SidebarMenuButton as-child>
                        <NuxtLink
                          to="/schedule/next"
                          @click="closeSideBar"
                        >
                          <CalendarPlus /> Next
                        </NuxtLink>
                      </SidebarMenuButton>
                    </SidebarMenuSubItem>
                    <SidebarMenuSubItem>
                      <SidebarMenuButton as-child>
                        <NuxtLink
                          to="/schedule/view"
                          @click="closeSideBar"
                        >
                          <Calendar /> Drivers
                        </NuxtLink>
                      </SidebarMenuButton>
                    </SidebarMenuSubItem>
                  </SidebarMenuSub>
                </CollapsibleContent>
              </SidebarMenuItem>
            </Collapsible>
            <SidebarMenuItem v-if="showPersonal">
              <SidebarMenuButton as-child>
                <NuxtLink to="/personal" @click="closeSideBar" class="flex items-center justify-between w-full">
                  <div class="flex items-center gap-2">
                    <User />
                    <span>Documents</span>
                  </div>

                  <div v-if="documentStatus !== 'clear'" class="flex items-center pr-1">
                    <div 
                      class="flex items-center justify-center p-1 rounded-full animate-pulse shadow-sm"
                      :class="documentStatus === 'expired' ? 'bg-red-500 text-white' : 'bg-amber-500 text-white'"
                    >
                      <AlertCircle :size="12" :stroke-width="3" />
                    </div>
                  </div>
                </NuxtLink>
              </SidebarMenuButton>
            </SidebarMenuItem>
          </SidebarMenu>
        </SidebarGroupContent>
      </SidebarGroup>
    </SidebarContent>
    <SidebarFooter>
      <SidebarMenuButton
        class="cursor-pointer"
        @click="logout"
      >
        <LogOut />
        Log out
      </SidebarMenuButton>
    </SidebarFooter>
  </Sidebar>
</template>