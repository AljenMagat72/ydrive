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
  
const fullName = computed(() => props.details?.Full_Name || '---');
const phone = computed(() => props.details?.Phone || '---');
const dob = computed(() => props.details?.Date_of_Birth || '---');
const licenseNo = computed(() => props.details?.Account || '---');
const licenseClass = computed(() => props.details?.License_Class || '---');
const licenseExp = computed(() => props.details?.License_Exp || '---');
const fileInput = ref<HTMLInputElement | null>(null);
const selectedFile = ref<File | null>(null);
const selectedImage = ref<string | null>(null)
const triggerFileSelect = () => {
  fileInput.value?.click();
};

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

const licenseStatus = computed(() => {
  const expDateStr = props.details?.License_Exp;
  if (!expDateStr) return { label: 'Missing', class: 'bg-gray-500/20 text-gray-400 border-gray-500/30' };

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
  const subject = encodeURIComponent(`Drivers License Update Request - ${props.details?.Full_Name || 'Driver'}`);
  
  let bodyText = `Hello,\n\nI would like to request a banking update.\n\n`;
  bodyText += `Driver Name: ${props.details?.Full_Name}\n`;
  bodyText += `License Class: ${licenseClass.value}\n`;
  bodyText += `License Expiration: ${licenseExp.value}\n\n`;
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
    <div class="flex items-center justify-between w-full gap-2">
      <div class="flex items-center gap-2 min-w-0">
        <div class="bg-blue-600/20 p-1.5 rounded-lg shrink-0">
          <IdCard class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500" />
        </div>
        <span class="text-lg sm:text-xl font-semibold tracking-tight text-white truncate">
          Driver License
        </span>
      </div>

      <div class="shrink-0">
        <span :class="[
          licenseStatus.class, 
          'px-2 sm:px-3 py-1 rounded-full text-[10px] sm:text-sm font-medium border whitespace-nowrap'
        ]">
          {{ licenseStatus.label }}
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

      <div class="flex flex-col justify-center space-y-2 w-full">
        <div class="space-y-1">
          <p class="text-white text-sm flex justify-between sm:justify-between gap-2">
            <span class="font-semibold text-white">Fullname:</span> 
            <span>{{ fullName || 'N/A' }}</span>
          </p>
          <p class="text-white text-sm flex justify-between sm:justify-between gap-2">
            <span class="font-semibold text-white">Phone Number:</span> 
            <span>{{ phone || 'N/A' }}</span>
          </p>
          <p class="text-white text-sm flex justify-between sm:justify-between gap-2">
            <span class="font-semibold text-white">Birth date:</span> 
            <span>{{ dob || 'N/A' }}</span>
          </p>
          <p class="text-white text-sm flex justify-between sm:justify-between items-center gap-2">
            <span class="font-semibold text-white">License Class:</span> 
            <span class="justify-center">{{ licenseClass || 'N/A' }}</span>
          </p>
          <p class="text-white text-sm flex justify-between sm:justify-between items-center gap-2">
            <span class="font-semibold text-white">License Expiration:</span> 
            <span class="justify-center">{{ licenseExp || 'N/A' }}</span>
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

