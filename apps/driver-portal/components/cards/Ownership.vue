<script setup lang="ts">
import { IdCard } from 'lucide-vue-next';
import { computed, ref, onMounted } from 'vue';

interface Props {
  details: any;
  loading: boolean;
}

const props = defineProps<{
  details: any
}>()

const carAttachmentId = ref<string | null>(null)
  
const RegistrationExp = computed(() => props.details?.Registration_Exp || '---');

onMounted(async () => {
  if (props.details?.id) {
    try {
      // This calls your Laravel route: Route::get('/driver-documents/{zohoId}', ...)
      const response = await $fetch<any>(`http://localhost:8000/api/api/driver-documents/${props.details.id}`)
      
      // Look for a file in the attachments list that has "car" in the name
      const file = response.data?.find((f: any) => 
        f.File_Name.toLowerCase().includes('car')
      )
      
      if (file) {
        carAttachmentId.value = file.id
      }
    } catch (e) {
      console.error("Could not load car photo ID", e)
    }
  }
})

const validity = computed(() => {
  const expDateStr = props.details?.Registration_Exp;
  
  if (!expDateStr) return { label: 'Invalid', class: 'bg-red-500/20 text-red-400 border-red-500/30' };

  const expDate = new Date(expDateStr);
  const today = new Date();
  
  // Set times to midnight for accurate day comparison
  today.setHours(0, 0, 0, 0);
  expDate.setHours(0, 0, 0, 0);

  const diffTime = expDate.getTime() - today.getTime();
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

  if (diffDays < 0) {
    return { label: 'Expired', class: 'bg-red-500/20 text-red-400 border-red-500/30' };
  } else if (diffDays <= 30) {
    return { label: 'Expiring Soon', class: 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30' };
  } else {
    return { label: 'Valid', class: 'bg-green-500/20 text-green-400 border-green-500/30' };
  }
});
</script>

<template>
  <div class="bg-black border border-gray-800 rounded-2xl p-6 flex flex-col gap-y-4 w-full max-w-sm shadow-blue h-full">
    
    <div class="flex items-center justify-between w-full gap-2">
        <div class="flex items-center gap-2 min-w-0">
            <div class="bg-blue-600/20 p-1.5 rounded-lg shrink-0">
            <IdCard class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500" />
            </div>
            <span class="text-lg sm:text-xl font-semibold tracking-tight text-white truncate">
            Ownership
            </span>
        </div>

        <div class="shrink-0">
            <span :class="[
            validity.class, 
            'px-2 sm:px-3 py-1 rounded-full text-[10px] sm:text-sm font-medium border whitespace-nowrap'
            ]">
            {{ validity.label }}
            </span>
        </div>
    </div>

    <div class="flex flex-col sm:flex-col gap-6 mt-2">
      
      <div class="mx-auto w-full sm:w-48 h-32 shrink-0 border-2 border-dashed border-gray-700 rounded-xl flex items-center justify-center bg-gray-900/50 overflow-hidden">
        <img 
          v-if="carAttachmentId"
          :src="`http://localhost:8000/api/view-attachment/${details.id}/${carAttachmentId}`" 
          class="w-full h-full object-cover" 
        />
        <div v-else class="text-gray-600 text-xs text-center uppercase font-bold tracking-widest px-4">
          No Image on File
        </div>
      </div>

      <div class="flex flex-col justify-center space-y-2 w-full flex-grow">
        <div class="space-y-1">
          <p class="text-white text-sm flex justify-between sm:justify-between gap-2">
            <span class="font-semibold text-white">Registration Expiry:</span> 
            <span>{{ RegistrationExp || 'N/A' }}</span>
          </p>
        </div>
      </div>
    </div>

    <div class="flex flex-col mt-auto">
        <button class="mt-2 w-full sm:w-auto bg-white text-black font-semibold py-2 px-6 rounded-full hover:text-white hover:bg-blue-600 transition-colors ">
        Request Update
        </button>
    </div>
  </div>
</template>

