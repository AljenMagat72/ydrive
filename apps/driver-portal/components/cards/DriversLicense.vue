<script setup lang="ts">
import { useZoho } from '#imports';
import { IdCard, CheckCircle2, AlertCircle, X, Upload } from 'lucide-vue-next';
import { computed, ref, watch, onBeforeUnmount } from 'vue';

const props = defineProps<{
  details: any
}>()

const { fullName, phone, dob, licenseClass, licenseExp, fetchSecureImage, uploadDocuments, refresh } = useZoho();

const fileInput = ref<HTMLInputElement | null>(null);
const isUploading = ref(false);
const showToast = ref(false);
const toastMessage = ref('');
const toastType = ref<'success' | 'error'>('success');

const licenseDocId = computed<string | null>(() => {
  const doc = props.details?.Drivers_License;
  if (Array.isArray(doc) && doc.length > 0) {
    return doc[0].File_Id__s || doc[0].id || null;
  }
  return typeof doc === 'string' ? doc : null;
});

const imageLoaded = ref(false);
const imageBlobUrl = ref<string | null>(null);
const isModalOpen = ref(false);

const loadImage = async () => {
  if (!licenseDocId.value) {
    imageLoaded.value = true;
    return;
  }
  
  imageLoaded.value = false;
  try {
    if (imageBlobUrl.value) URL.revokeObjectURL(imageBlobUrl.value);
    const url = await fetchSecureImage(licenseDocId.value as string);
    if (url) imageBlobUrl.value = url;
  } catch (err) {
    console.error("License Image Load Error:", err);
  } finally {
    imageLoaded.value = true;
  }
};

watch(() => licenseDocId.value, (newId) => {
  if (newId) loadImage();
  else imageLoaded.value = true;
}, { immediate: true });

onBeforeUnmount(() => {
  if (imageBlobUrl.value) URL.revokeObjectURL(imageBlobUrl.value);
});

const toggleModal = () => {
  if (imageBlobUrl.value) isModalOpen.value = !isModalOpen.value;
}

const validity = computed(() => {
  const expDateStr = licenseExp.value;
  if (!expDateStr) return { label: 'Missing', class: 'bg-gray-500/20 text-gray-400 border-gray-500/30', isValid: false };

  const expDate = new Date(expDateStr);
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  expDate.setHours(0, 0, 0, 0);

  const diffDays = Math.ceil((expDate.getTime() - today.getTime()) / (1000 * 60 * 60 * 24));

  if (diffDays < 0) return { label: 'Expired', class: 'bg-red-500/20 text-red-400 border-red-500/30', isValid: false };
  if (diffDays <= 30) return { label: 'Expiring Soon', class: 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30', isValid: false };
  return { label: 'Valid', class: 'bg-green-500/20 text-green-400 border-green-500/30', isValid: true };
});

const isButtonDisabled = computed(() => isUploading.value || (validity.value.isValid && !!licenseDocId.value));

const triggerUpload = () => {
  if (!isButtonDisabled.value) fileInput.value?.click();
};

const triggerToast = (msg: string, type: 'success' | 'error' = 'success') => {
  toastMessage.value = msg;
  toastType.value = type;
  showToast.value = true;
  setTimeout(() => showToast.value = false, 4000);
};

const handleFileUpload = async (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (!target.files?.length) return;

  const rawFiles = Array.from(target.files);
  isUploading.value = true;

  // Formatting the date as YYYY-MM-DD
  const today = new Date().toISOString().split('T')[0];
  
  // Sanitize full name (remove spaces/special chars for filename safety)
  const sanitizedName = (fullName.value || 'Unknown_Driver')
    .trim()
    .replace(/\s+/g, '_')
    .replace(/[^a-zA-Z0-9_]/g, '');

  const renamedFiles = rawFiles.map((file, index) => {
    const extension = file.name.split('.').pop();
    const suffix = rawFiles.length > 1 ? `_${index + 1}` : '';
    const newFileName = `DriversLicense_${sanitizedName}_${today}${suffix}.${extension}`;
    
    return new File([file], newFileName, { type: file.type });
  });

  try {
    await uploadDocuments(renamedFiles, 'TBU_Drivers_License'); 
    triggerToast('Drivers License photo uploaded successfully!', 'success');
    if (refresh) await refresh();
  } catch (err: any) {
    triggerToast(err.data?.message || 'Upload failed.', 'error');
  } finally {
    isUploading.value = false;
    if (target) target.value = '';
  }
};
</script>

<template>
  <div class="bg-black border border-gray-800 rounded-2xl p-6 flex flex-col gap-4 w-full max-w-sm shadow-blue h-full relative">
    
    <input 
      type="file" 
      ref="fileInput" 
      class="hidden" 
      accept="image/*,application/pdf" 
      multiple
      @change="handleFileUpload" 
    />

    <div class="flex flex-center items-center justify-between w-full gap-2">
      <div class="flex items-center gap-2 min-w-0">
        <div class="bg-blue-600/20 p-1.5 rounded-lg shrink-0">
          <IdCard class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500" />
        </div>
        <span class="text-lg sm:text-xl font-semibold tracking-tight text-white truncate">Drivers License</span>
      </div>

      <div class="shrink-0">
        <span :class="[validity.class, 'px-2 sm:px-3 py-1 rounded-full text-[10px] sm:text-sm font-medium border whitespace-nowrap']">
          {{ validity.label }}
        </span>
      </div>
    </div>

    <div class="flex flex-col gap-6 mt-2">
      <div class="relative mx-auto w-full sm:w-48 h-32 shrink-0 border-2 border-dashed border-gray-700 rounded-xl flex items-center justify-center bg-gray-900/50 overflow-hidden group">
        <div v-if="licenseDocId && !imageLoaded" class="absolute inset-0 flex items-center justify-center bg-gray-900/80 z-10">
          <div class="w-6 h-6 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
        </div>
        
        <img 
          v-if="imageBlobUrl"
          :src="imageBlobUrl"
          @click="toggleModal"
          @load="imageLoaded = true"
          v-show="imageLoaded"
          class="w-full h-full object-contain cursor-pointer transition-transform duration-300 group-hover:scale-105" 
        />
        <div v-else-if="!licenseDocId" class="text-gray-600 text-[10px] text-center uppercase font-bold tracking-widest px-4">
          No Image on File
        </div>
      </div>

      <div class="flex flex-col justify-top space-y-2 w-full flex-grow">
        <div class="space-y-1">
          <p class="text-white text-sm flex justify-between gap-2">
            <span class="font-semibold text-gray-400">Fullname:</span> 
            <span class="truncate">{{ fullName || 'N/A' }}</span>
          </p>
          <p class="text-white text-sm flex justify-between gap-2">
            <span class="font-semibold text-gray-400">Expiration:</span> 
            <span>{{ licenseExp || 'N/A' }}</span>
          </p>
        </div>
      </div>
      
      <div class="flex flex-col mt-auto">
        <button 
          @click="triggerUpload" 
          :disabled="isButtonDisabled"
          :class="[
            isButtonDisabled 
              ? 'bg-gray-800 text-blue-500/50 cursor-not-allowed border border-blue-500/30' 
              : 'bg-blue-600 text-white hover:bg-blue-700 active:scale-95 shadow-lg shadow-blue-600/20',
            'mt-2 w-full font-semibold py-2.5 px-6 rounded-full transition-all duration-200 flex items-center justify-center gap-2'
          ]"
        >
          <span v-if="isUploading" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
          <template v-else>
            <CheckCircle2 v-if="validity.isValid && licenseDocId" class="w-4 h-4 text-blue-500" />
            <Upload v-else class="w-4 h-4" />
            <span>{{ (validity.isValid && licenseDocId) ? 'Verified' : 'Upload New License' }}</span>
          </template>
        </button>
        <p class="text-[10px] text-gray-500 text-center uppercase tracking-widest mt-2">
          for Admin Verification
        </p>
      </div>
    </div>

    <Teleport to="body">
      <Transition 
        enter-active-class="transform transition duration-500 ease-out" 
        enter-from-class="translate-x-20 opacity-0 scale-95" 
        enter-to-class="translate-x-0 opacity-100 scale-100" 
        leave-active-class="transition duration-300 ease-in" 
        leave-from-class="opacity-100" 
        leave-to-class="opacity-0"
      >
        <div 
          v-if="showToast" 
          class="fixed top-6 right-6 z-[200] flex items-center gap-3 px-6 py-4 rounded-2xl border shadow-2xl min-w-[320px] backdrop-blur-md" 
          :class="toastType === 'success' ? 'bg-green-600/90 border-green-400 text-white' : 'bg-red-600/90 border-red-400 text-white'"
        >
          <CheckCircle2 v-if="toastType === 'success'" class="w-5 h-5 text-white" />
          <AlertCircle v-else class="w-5 h-5 text-white" />
          <span class="text-sm font-semibold">{{ toastMessage }}</span>
          <button @click="showToast = false" class="ml-auto p-1.5 hover:bg-white/10 rounded-full transition-colors">
            <X class="w-4 h-4" />
          </button>
        </div>
      </Transition>

      <Transition 
        enter-active-class="transition duration-200 ease-out" 
        enter-from-class="opacity-0" 
        enter-to-class="opacity-100" 
        leave-active-class="transition duration-150 ease-in" 
        leave-from-class="opacity-100" 
        leave-to-class="opacity-0"
      >
        <div 
          v-if="isModalOpen" 
          class="fixed inset-0 z-[100] flex items-center justify-center bg-black/95 backdrop-blur-sm p-4 md:p-10" 
          @click="toggleModal"
        >
          <button class="absolute top-6 right-6 text-white/50 hover:text-white transition-colors">
            <X class="w-8 h-8" />
          </button>
          <img 
            v-if="imageBlobUrl" 
            :src="imageBlobUrl" 
            class="max-w-full max-h-full object-contain shadow-2xl rounded-lg border border-white/10" 
            @click.stop 
          />
        </div>
      </Transition>
    </Teleport>
  </div>
</template>