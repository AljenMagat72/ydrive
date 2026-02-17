<script setup lang="ts">
import { useZoho } from '#imports';
import { IdCard } from 'lucide-vue-next';
import { computed, ref, watch, onBeforeUnmount } from 'vue';

const props = defineProps<{
  details: any
}>()

const { registrationExp, fetchSecureImage } = useZoho();

const ownershipDocId = computed<string | null>(() => {
  const doc = props.details?.Vehicle_Ownership;
  
  if (Array.isArray(doc) && doc.length > 0) {
    return doc[0].File_Id__s || doc[0].id || null;
  }
  
  return typeof doc === 'string' ? doc : null;
});

const imageLoaded = ref(false);
const imageBlobUrl = ref<string | null>(null);
const isModalOpen = ref(false);

const loadImage = async () => {
  if (!ownershipDocId.value) {
    imageLoaded.value = true;
    return;
  }
  
  imageLoaded.value = false;
  try {
    const url = await fetchSecureImage(ownershipDocId.value as string);
    if (url) {
      imageBlobUrl.value = url;
    }
  } catch (err) {
    console.error("Ownership Image Load Error:", err);
  } finally {
    imageLoaded.value = true;
  }
};

watch(() => ownershipDocId.value, (newId) => {
  if (newId) loadImage();
  else imageLoaded.value = true;
}, { immediate: true });

onBeforeUnmount(() => {
  if (imageBlobUrl.value) URL.revokeObjectURL(imageBlobUrl.value);
});

const toggleModal = () => {
  if (imageBlobUrl.value) isModalOpen.value = !isModalOpen.value;
}

watch(isModalOpen, (isOpen) => {
  document.body.style.overflow = isOpen ? 'hidden' : '';
});

const validity = computed(() => {
  const expDateStr = registrationExp.value;
  if (!expDateStr || expDateStr === '---') {
    return { label: 'Missing', class: 'bg-gray-500/20 text-gray-400 border-gray-500/30' };
  }

  const expDate = new Date(expDateStr);
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  expDate.setHours(0, 0, 0, 0);

  const diffDays = Math.ceil((expDate.getTime() - today.getTime()) / (1000 * 60 * 60 * 24));

  if (diffDays < 0) return { label: 'Expired', class: 'bg-red-500/20 text-red-400 border-red-500/30' };
  if (diffDays <= 30) return { label: 'Expiring Soon', class: 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30' };
  return { label: 'Valid', class: 'bg-green-500/20 text-green-400 border-green-500/30' };
});

const sendEmail = () => {
  const email = 'mary@ydrive.com';
  const subject = encodeURIComponent(`Ownership Update Request - ${props.details?.Full_Name || 'Driver'}`);
  let bodyText = `Hello,\n\nI would like to request an Ownership update.\n\nDriver Name: ${props.details?.Full_Name}\nRegistration Expiration: ${registrationExp.value}\n\n[IMPORTANT]: I have attached my new details to this email.`;
  window.location.href = `mailto:${email}?subject=${subject}&body=${encodeURIComponent(bodyText)}`;
};
</script>

<template>
  <div class="bg-black border border-gray-800 rounded-2xl p-6 flex flex-col gap-y-4 w-full max-w-sm shadow-blue h-full">
    
    <div class="flex items-center justify-between w-full gap-2">
      <div class="flex items-center gap-2 min-w-0">
        <div class="bg-blue-600/20 p-1.5 rounded-lg shrink-0">
          <IdCard class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500" />
        </div>
        <span class="text-lg sm:text-xl font-semibold tracking-tight text-white truncate">Ownership</span>
      </div>
      <div class="shrink-0">
        <span :class="[validity.class, 'px-2 sm:px-3 py-1 rounded-full text-[10px] sm:text-sm font-medium border whitespace-nowrap']">
          {{ validity.label }}
        </span>
      </div>
    </div>

    <div class="flex flex-col gap-6 mt-2">
      <div class="relative mx-auto w-full sm:w-48 h-32 shrink-0 border-2 border-dashed border-gray-700 rounded-xl flex items-center justify-center bg-gray-900/50 overflow-hidden group">
        
        <div v-if="ownershipDocId && !imageLoaded" class="absolute inset-0 flex items-center justify-center bg-gray-900/80 z-10">
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
        
        <div 
          v-if="imageBlobUrl && imageLoaded" 
          @click="toggleModal"
          class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer"
        >
          <span class="text-white text-[10px] font-bold uppercase tracking-widest text-center px-2">View Document</span>
        </div>

        <div v-else-if="!ownershipDocId" class="text-gray-600 text-[10px] text-center uppercase font-bold tracking-widest px-4">
          No Image on File
        </div>
      </div>

      <div class="flex flex-col justify-top space-y-2 w-full flex-grow">
        <div class="space-y-1">
          <p class="text-white text-sm flex justify-between gap-2">
            <span class="font-semibold text-white">Registration Expiry:</span> 
            <span>{{ registrationExp || 'N/A' }}</span>
          </p>
        </div>
      </div>
    </div>

    <div class="flex flex-col mt-auto">
      <button @click="sendEmail" class="mt-2 w-full sm:w-auto bg-white text-black font-semibold py-2 px-6 rounded-full hover:text-white hover:bg-blue-600 transition-colors">
        Request Update
      </button>
    </div>

    <Teleport to="body">
      <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="isModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/95 backdrop-blur-sm p-4 md:p-10" @click="toggleModal">
          <button class="absolute top-6 right-6 text-white/70 hover:text-white transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
          <img v-if="imageBlobUrl" :src="imageBlobUrl" class="max-w-full max-h-full object-contain shadow-2xl rounded-lg border border-white/10" @click.stop />
        </div>
      </Transition>
    </Teleport>
  </div>
</template>