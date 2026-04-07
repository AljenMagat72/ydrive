<script setup lang="ts">
import { Calendar, CalendarClock, CalendarPlus, ChevronRight, LogOut, User, Moon, 
  Sun } from 'lucide-vue-next';
import { useAuth, useRuntimeConfig, useColorMode } from '#imports';
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

const { user, logout } = useAuth();
const { setOpenMobile } = useSidebar();
const config = useRuntimeConfig();
const showPersonal = computed(() => config.public.showZohoDocs);

function closeSideBar() {
  setOpenMobile(false);
}

const colorMode = useColorMode();

const toggleTheme = () => {
  colorMode.preference = colorMode.preference === 'dark' ? 'light' : 'dark';
};
</script>

<template>
  <Sidebar>
    <SidebarHeader>
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
    </SidebarHeader>
    <SidebarContent>
      <SidebarGroup>
        <SidebarGroupContent>
          <SidebarMenu>

            <SidebarMenuItem>
              <SidebarMenuButton class="cursor-pointer" @click="toggleTheme">
                <template v-if="colorMode.preference === 'dark'">
                  <Moon /> <span>Dark Mode</span>
                </template>
                <template v-else>
                  <Sun /> <span>Light Mode</span>
                </template>
              </SidebarMenuButton>
            </SidebarMenuItem>
            
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
                <NuxtLink to="/personal" @click="closeSideBar">
                  <User /> 
                  <span>Personal</span>
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