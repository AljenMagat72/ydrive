<script lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

export default {
  layout: AppLayout,
}
</script>

<script setup lang="ts">
import ScheduleWeek from '@/components/ScheduleWeek.vue';
</script>

<template>
  <Head title="Driver | Schedule" />
  <div class="flex justify-center">
    <ScheduleWeek class="w-full max-w-lg" />
  </div>
</template>
