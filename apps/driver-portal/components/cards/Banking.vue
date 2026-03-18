<script setup lang="ts">
import { useZoho } from '#imports';
import { Landmark, Mail } from 'lucide-vue-next';

const props = defineProps<{
  details: any
}>()

const { bankName, bankAccount } = useZoho();

const sendEmail = () => {
  const email = 'mary@ydrive.com';
  const subject = encodeURIComponent(`Banking Update Request - ${props.details?.Full_Name || 'Driver'}`);
  
  let bodyText = `Hello,\n\nI would like to request a Banking update.\n\n`;
  bodyText += `Driver Name: ${props.details?.Full_Name}\n`;
  bodyText += `Current Bank: ${bankName.value}\n`;
  bodyText += `Current Account: ${bankAccount.value}\n\n`;
  bodyText += `[IMPORTANT]: I have attached my new details to this email.`;

  window.location.href = `mailto:${email}?subject=${subject}&body=${encodeURIComponent(bodyText)}`;
};
</script>

<template>
  <div class="bg-black border border-gray-800 rounded-2xl p-6 flex flex-col gap-4 w-full max-w-sm shadow-blue h-full relative">

    <div class="flex items-center justify-center w-full gap-2 relative">
      <div class="flex items-center gap-2">
        <div class="bg-blue-600/20 p-1.5 rounded-lg shrink-0">
          <Landmark class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500" />
        </div>
        <span class="text-lg sm:text-xl font-semibold tracking-tight text-white truncate">Banking</span>
      </div>
    </div>

    <div class="flex flex-col gap-6 mt-2 flex-grow">
      
      <div class="mx-auto w-full sm:w-48 h-32 shrink-0 border-2 border-dashed border-gray-700 rounded-xl flex items-center justify-center bg-gray-900/50 overflow-hidden">
        <div class="text-gray-600 text-[10px] text-center uppercase font-bold tracking-widest px-4">
          No Document on File
        </div>
      </div>

      <div class="flex flex-col justify-top space-y-2 w-full">
        <div class="space-y-1">
          <p class="text-white text-sm flex justify-between gap-2">
            <span class="font-semibold text-gray-400">Bank Name:</span> 
            <span class="truncate text-right text-white">{{ bankName || 'yDrive Bank' }}</span>
          </p>
          <p class="text-white text-sm flex justify-between gap-2">
            <span class="font-semibold text-gray-400">Bank Account:</span> 
            <span class="text-right text-white">{{ bankAccount || '---' }}</span>
          </p>
        </div>
      </div>
      
      <div class="flex flex-col mt-auto">
        <button 
          @click="sendEmail"
          class="w-full bg-blue-600 text-white font-semibold py-2.5 px-6 rounded-full hover:bg-blue-700 active:scale-95 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-blue-600/20"
        >
          <Mail class="w-4 h-4" />
          <span>Request Banking Update</span>
        </button>
        <p class="text-[10px] text-gray-500 text-center uppercase tracking-widest mt-2">
          via Administration
        </p>
      </div>
    </div>
  </div>
</template>