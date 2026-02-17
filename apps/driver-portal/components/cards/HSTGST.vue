<script setup lang="ts">
import { useZoho } from '#imports';
import { Receipt } from 'lucide-vue-next';

const props = defineProps<{
  details: any
}>()

const { hstGst } = useZoho();

const sendEmail = () => {
  const email = 'mary@ydrive.com';
  const subject = encodeURIComponent(`HST/GST Update Request - ${props.details?.Full_Name || 'Driver'}`);
  
  let bodyText = `Hello,\n\nI would like to request a HST/GST update.\n\n`;
  bodyText += `Driver Name: ${props.details?.Full_Name}\n`;
  bodyText += `Current HST/GST: ${hstGst.value}\n\n`;
  bodyText += `[IMPORTANT]: I have attached my new details to this email.`;

  window.location.href = `mailto:${email}?subject=${subject}&body=${encodeURIComponent(bodyText)}`;
};
</script>

<template>
  <div class="bg-black border border-gray-800 rounded-2xl p-6 flex flex-col gap-4 w-full max-w-sm shadow-blue h-full">
    
    <div class="flex items-center justify-between w-full gap-2">
      <div class="flex items-center gap-2 min-w-0">
        <div class="bg-blue-600/20 p-1.5 rounded-lg shrink-0">
          <Receipt class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500" />
        </div>
        <span class="text-lg sm:text-xl font-semibold tracking-tight text-white truncate">
          HST/GST
        </span>
      </div>
    </div>

    <div class="flex flex-col gap-6 mt-2 flex-grow">
      
      <div class="mx-auto w-full sm:w-48 h-32 shrink-0 border-2 border-dashed border-gray-700 rounded-xl flex items-center justify-center bg-gray-900/50 overflow-hidden">
        <div class="text-gray-600 text-[10px] text-center uppercase font-bold tracking-widest px-4">
          No Image on File
        </div>
      </div>

      <div class="flex flex-col justify-top space-y-2 w-full flex-grow">
        <div class="space-y-1">
          <p class="text-white text-sm flex justify-between gap-2">
            <span class="font-semibold text-white">Tax ID:</span> 
            <span class="truncate text-right">{{ hstGst || '---' }}</span>
          </p>
        </div>
      </div>
      
      <div class="flex flex-col mt-auto">
        <button 
          @click="sendEmail"
          class="mt-2 w-full sm:w-auto bg-white text-black font-semibold py-2 px-6 rounded-full hover:text-white hover:bg-blue-600 transition-colors"
        >
          Request Update
        </button>
      </div>
    </div>
  </div>
</template>