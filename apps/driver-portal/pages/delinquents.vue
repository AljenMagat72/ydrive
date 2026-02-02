<script setup lang="ts">
import { definePageMeta } from '#imports';
import DelinquentsTable from '~/components/delinquents/DelinquentsTable.vue';

definePageMeta({
  layout: 'empty',
  middleware: ['chart-key']
});
</script>

<template>
  <div class="flex flex-col h-dvh gap-y-4 p-4">
    <ClientOnly>
      <div class="flex gap-x-4">
        <DelinquentsTable />
      </div>
    </ClientOnly>
  </div>
</template>