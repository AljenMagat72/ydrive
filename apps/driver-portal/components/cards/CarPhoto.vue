<script setup lang="ts">
import { useZoho } from '#imports';
import { Car } from 'lucide-vue-next';
import { computed, ref, watch, onBeforeUnmount } from 'vue';

const props = defineProps<{
  details: any
}>()

const { make, model, year, fetchSecureImage } = useZoho();

const carAttachmentId = computed<string | null>(() => {
  const doc = props.details?.Car_Photo;
  
  if (Array.isArray(doc) && doc.length > 0) {
    return doc[0].File_Id__s || doc[0].id || null;
  }
  
  return typeof doc === 'string' ? doc : null;
});

const imageLoaded = ref(false);
const imageBlobUrl = ref<string | null>(null);
const isModalOpen = ref(false);

const loadImage = async () => {
  if (!carAttachmentId.value) {
    imageLoaded.value = true;
    return;
  }
  
  imageLoaded.value = false;
  try {
    const url = await fetchSecureImage(carAttachmentId.value as string);
    if (url) {
      imageBlobUrl.value = url;
    }
  } catch (err) {
    console.error("Car Photo Load Error:", err);
  } finally {
    imageLoaded.value = true;
  }
};

watch(() => carAttachmentId.value, (newId) => {
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

const sendEmail = () => {
  const email = 'mary@ydrive.com';
  const subject = encodeURIComponent(`Car Details Update Request - ${props.details?.Full_Name || 'Driver'}`);
  let bodyText = `Hello,\n\nI would like to request a Car Details update.\n\nDriver Name: ${props.details?.Full_Name}\nCar: ${make.value} ${model.value} (${year.value})`;
  window.location.href = `mailto:${email}?subject=${subject}&body=${encodeURIComponent(bodyText)}`;
};
</script>

<template>
  <div class="bg-black border border-gray-800 rounded-2xl p-6 flex flex-col gap-y-4 w-full max-w-sm shadow-blue h-full">
    
    <div class="flex items-center justify-center w-full gap-2">
      <div class="bg-blue-600/20 p-1.5 rounded-lg shrink-0">
        <Car class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500" />
      </div>
      <span class="text-lg sm:text-xl font-semibold tracking-tight text-white truncate">Car Photo</span>
    </div>

    <div class="flex flex-col gap-6 mt-2">
      <div class="relative mx-auto w-full sm:w-48 h-32 shrink-0 border-2 border-dashed border-gray-700 rounded-xl flex items-center justify-center bg-gray-900/50 overflow-hidden group">
        
        <div v-if="carAttachmentId && !imageLoaded" class="absolute inset-0 flex items-center justify-center bg-gray-900/80 z-10">
          <div class="w-6 h-6 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
        </div>

        <img 
          v-if="imageBlobUrl"
          :src="imageBlobUrl"
          @click="toggleModal"
          @load="imageLoaded = true"
          v-show="imageLoaded"
          class="w-full h-full object-cover cursor-pointer transition-transform duration-300 group-hover:scale-105" 
        />
        
        <div 
          v-if="imageBlobUrl && imageLoaded" 
          @click="toggleModal"
          class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer"
        >
          <span class="text-white text-[10px] font-bold uppercase tracking-widest text-center px-2">View Car</span>
        </div>

        <div v-else-if="!carAttachmentId" class="text-gray-600 text-[10px] text-center uppercase font-bold tracking-widest px-4">
          No Photo on File
        </div>
      </div>

      <div class="flex flex-col justify-top space-y-2 w-full flex-grow">
        <div class="space-y-1">
          <p class="text-white text-sm flex justify-between gap-2">
            <span class="font-semibold text-white">Make:</span> 
            <span>{{ make || 'N/A' }}</span>
          </p>
          <p class="text-white text-sm flex justify-between gap-2">
            <span class="font-semibold text-white">Model:</span> 
            <span>{{ model || 'N/A' }}</span>
          </p>
          <p class="text-white text-sm flex justify-between gap-2">
            <span class="font-semibold text-white">Year:</span> 
            <span>{{ year || 'N/A' }}</span>
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