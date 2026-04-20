<script setup lang="ts">
import { ref, onMounted } from "vue";
import { fetchAllDrivers } from "~/lib/api/drivers";
import DriversDocuments from "~/components/DriversDocuments.vue";

definePageMeta({
  layout: "auth",
  layoutTransition: {
    name: "slide-up",
    mode: "out-in",
  },
  middleware: ["auth"],
});

const drivers = useState<any[]>('drivers', () => []);
const loading = ref(false);;

const loadInitialDrivers = async () => {
  if (drivers.value.length > 0) return;

  try {
    loading.value = true;
    const data = await fetchAllDrivers();
    drivers.value = data.drivers || [];
  } catch (err) {
    console.error("Failed to load driver list:", err);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadInitialDrivers();
});
</script>

<template>
  <div class="p-4">
    <div v-if="loading" class="flex justify-center py-20">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
    </div>
    
    <DriversDocuments v-else :drivers="drivers" />
  </div>
</template>