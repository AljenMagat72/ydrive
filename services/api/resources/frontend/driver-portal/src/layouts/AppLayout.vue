<script setup lang="ts">
import { SidebarProvider, SidebarTrigger } from '@/components/ui/sidebar';
import AppSidebar from '@/components/AppSidebar.vue';

import { useAppearance } from '@/composables/use-appearance';
import { Button } from '@/components/ui/button';
import { Palette } from 'lucide-vue-next';
import { usePage, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Breadcrumb, BreadcrumbItem, BreadcrumbList, BreadcrumbPage, BreadcrumbSeparator, BreadcrumbLink } from '@/components/ui/breadcrumb';
import { Toaster } from 'vue-sonner';

const { toggleAppearance, resolvedAppearance } = useAppearance();

const page = usePage();

const breadcrumbs = computed(() => page.props.breadcrumbs ?? []);
</script>

<template>
  <Toaster :theme="resolvedAppearance" />
  <SidebarProvider>
    <AppSidebar />
    <main class="flex flex-col w-full">
      <header class="flex flex-row justify-between items-center gap-x-2 -ml-1 p-2">
        <SidebarTrigger />
        <Breadcrumb
          class="flex-1"
          v-if="breadcrumbs.length"
        >
          <BreadcrumbList>
            <template
              v-for="(crumb, index) in breadcrumbs"
              :key="index"
            >
              <BreadcrumbItem>
                <BreadcrumbLink
                  v-if="crumb.href && index !== breadcrumbs.length - 1"
                  :as="Link"
                  :href="crumb.href"
                >
                  {{ crumb.label }}
                </BreadcrumbLink>

                <BreadcrumbPage v-else>
                  {{ crumb.label }}
                </BreadcrumbPage>
              </BreadcrumbItem>

              <BreadcrumbSeparator v-if="index < breadcrumbs.length - 1" />
            </template>
          </BreadcrumbList>
        </Breadcrumb>
        <Button
          @click="toggleAppearance"
          size="icon-xs"
          variant="ghost"
        >
          <Palette />
        </Button>
      </header>
      <div class="flex-1 px-2 pb-2">
        <slot />
      </div>
    </main>
  </SidebarProvider>
</template>
