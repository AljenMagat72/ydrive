<script lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

export default {
  layout: AppLayout,
}
</script>

<script setup lang="ts">
  import CitySchedule from '@/components/city-schedule/CitySchedule.vue';
</script>

<template>
  <Head title="Driver | Schedule City" />
  <CitySchedule />
</template>
