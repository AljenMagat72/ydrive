<script setup lang="ts">
import { onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { Calendar, CalendarPlus, LogOut } from 'lucide-vue-next';
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
  useSidebar,
} from '@/components/ui/sidebar';
import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar';
import { dashboard, schedule } from '@/wayfinder/routes/driver';
import { city } from '@/wayfinder/routes/driver/schedule';

import { useDriverQuery } from '@/api/queries/use-driver-query';
import { useLogoutMutation } from '@/api/mutations/auth/use-logout-mutation';

const { setOpenMobile } = useSidebar();
const { data } = useDriverQuery();
const { mutateAsync: logout } = useLogoutMutation();

async function onLogout() {
  logout();
}

const unsubscribe = router.on('start', () => {
  setOpenMobile(false);
});

onUnmounted(unsubscribe);
</script>

<template>
  <Sidebar>
    <SidebarHeader>
      <Link :href="dashboard.url()">
        <div class="flex flex-row items-center gap-2">
          <Avatar class="size-10">
            <AvatarImage
              v-if="data?.avatar"
              :src="data?.avatar"
            />
            <AvatarFallback />
          </Avatar>

          <div class="text-sm">
            <p class="font-semibold">{{ data?.firstName }}</p>
            <p>{{ data?.lastName }}</p>
          </div>
        </div>
      </Link>
    </SidebarHeader>
    <SidebarContent>
      <SidebarGroup>
        <SidebarGroupContent>
          <SidebarMenu>
            <SidebarMenuItem>
              <SidebarMenuButton as-child>
                <Link :href="schedule.url()">
                  <CalendarPlus /> Schedule
                </Link>
              </SidebarMenuButton>
            </SidebarMenuItem>
            <SidebarMenuItem>
              <SidebarMenuButton as-child>
                <Link :href="city.url()">
                  <Calendar /> View
                </Link>
              </SidebarMenuButton>
            </SidebarMenuItem>
          </SidebarMenu>
        </SidebarGroupContent>
      </SidebarGroup>
    </SidebarContent>
    <SidebarFooter>
      <SidebarMenuButton @click="onLogout">
        <LogOut />
        Log out
      </SidebarMenuButton>
    </SidebarFooter>
  </Sidebar>
</template>
