<script setup lang="ts">
import { Calendar, CalendarClock, CalendarPlus, ChevronRight, LogOut } from 'lucide-vue-next';
import { useAuth } from '#imports';
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

const { user, logout } = useAuth();
const { setOpenMobile } = useSidebar();

function closeSideBar() {
  setOpenMobile(false);
}
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
            <!--<SidebarMenuItem>
              <SidebarMenuButton>
                <DollarSign /> Pay out
              </SidebarMenuButton>
            </SidebarMenuItem>-->
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