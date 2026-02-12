<script setup lang="ts">
import { Landmark } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps<{
  details: any
}>()
  
const bankName = computed(() => props.details?.Bank_Name || '---');
const bankAccount = computed(() => props.details?.Account || '---');
const selectedImage = ref<string | null>(null)
const carAttachmentId = ref<string | null>(null)
const fileInput = ref<HTMLInputElement | null>(null);
const selectedFile = ref<File | null>(null);
const triggerFileSelect = () => {
  fileInput.value?.click();
};

const onFileSelected = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    const file = target.files[0];
    selectedFile.value = file;
    selectedImage.value = URL.createObjectURL(file);
  }
};

const sendEmail = () => {
  const email = 'mary@ydrive.com';
  const subject = encodeURIComponent(`Banking Update Request - ${props.details?.Full_Name || 'Driver'}`);
  
  let bodyText = `Hello,\n\nI would like to request a banking update.\n\n`;
  bodyText += `Driver Name: ${props.details?.Full_Name}\n`;
  bodyText += `Current Bank: ${bankName.value}\n`;
  bodyText += `Current Account: ${bankAccount.value}\n\n`;
  bodyText += `[ACTION REQUIRED]: Please attach your new banking document/void cheque to this email before sending.`;

  const mailtoUrl = `mailto:${email}?subject=${subject}&body=${encodeURIComponent(bodyText)}`;
  
  window.location.href = mailtoUrl;
};
</script>

<template>
  <div class="bg-black border border-gray-800 rounded-2xl p-6 flex flex-col gap-4 w-full max-w-sm shadow-blue">
    <input 
      type="file" 
      ref="fileInput" 
      class="hidden" 
      accept="image/*" 
      @change="onFileSelected"
    />

    <div class="flex items-center justify-center w-full gap-2">
      <div class="flex items-center gap-2 min-w-0">
        <div class="bg-blue-600/20 p-1.5 rounded-lg shrink-0">
          <Landmark class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500" />
        </div>
        <span class="text-lg sm:text-xl font-semibold tracking-tight text-white truncate">
          Banking
        </span>
      </div>
    </div>

    <div class="flex flex-col gap-6 mt-2">
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

      <div class="flex flex-col justify-center space-y-4 w-full">
        <div class="space-y-1">
          <p class="text-white text-sm flex justify-between gap-2">
            <span class="font-semibold text-white">Bank Name:</span> 
            <span>{{ bankName }}</span>
          </p>
          <p class="text-white text-sm flex justify-between gap-2">
            <span class="font-semibold text-white">Bank Account:</span> 
            <span>{{ bankAccount }}</span>
          </p>
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
  </div>
</template>