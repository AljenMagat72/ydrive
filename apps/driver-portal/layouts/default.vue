<script setup lang="ts">
import { SidebarProvider, SidebarTrigger } from "~/components/ui/sidebar";
import AppSidebar from "~/components/AppSidebar.vue";
</script>

<template>
  <div class="min-h-dvh max-w-dvw bg-gradient-to-br from-background to-muted/80">
    <SidebarProvider class="flex-row">
      <AppSidebar />
      <main class="flex flex-col flex-1 overflow-x-hidden p-4">
        <SidebarTrigger class="mb-4" />
        <slot />
      </main>
    </SidebarProvider>
  </div>
</template>