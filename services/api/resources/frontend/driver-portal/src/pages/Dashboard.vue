<script lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';

export default {
  layout: AppLayout,
}
</script>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import { useSettingsQuery } from '@/api/queries/use-settings-query';

const { data } = useSettingsQuery('motd');

</script>

<template>
  <Head title="Driver | Dashboard" />
  <div
    class="space-y-2 max-w-3xl"
    v-html="data"
  />
</template>
