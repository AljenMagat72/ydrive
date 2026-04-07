<script setup lang="ts">
import { useZoho } from '#imports';
import { Receipt, Send, Mail, CheckCircle2, AlertCircle, X } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
  details: any
}>()

const { hstGst, updateProfile, refresh } = useZoho();

const isEditing = ref(false);
const isSaving = ref(false);
const newTaxId = ref('');

const showToast = ref(false);
const toastMessage = ref('');
const toastType = ref<'success' | 'error'>('success');

const triggerToast = (msg: string, type: 'success' | 'error' = 'success') => {
  toastMessage.value = msg;
  toastType.value = type;
  showToast.value = true;
  setTimeout(() => showToast.value = false, 4000);
};

const handleAction = async () => {
  if (!isEditing.value) {
    newTaxId.value = hstGst.value !== '---' ? hstGst.value : '';
    isEditing.value = true;
    return;
  }

  if (newTaxId.value) {
    isSaving.value = true;
    try {
      await updateProfile({
        HST_GST: newTaxId.value
      });

      triggerToast('Tax ID update submitted!', 'success');
      isEditing.value = false;
      if (refresh) await refresh();
    } catch (err: any) {
      console.error("Tax Update Error:", err);
      triggerToast(err.data?.message || 'Update failed.', 'error');
    } finally {
      isSaving.value = false;
    }
  }
};
</script>

<template>
  <div class="dark:bg-black border rounded-2xl p-6 flex flex-col gap-4 w-full max-w-sm shadow-blue h-full relative">
    
    <div class="flex items-center justify-center w-full gap-2 relative">
      <div class="flex items-center gap-2">
        <div class="bg-blue-600/20 p-1.5 rounded-lg shrink-0">
          <Receipt class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500" />
        </div>
        <span class="text-lg sm:text-xl font-semibold tracking-tight dark:text-white truncate">
          HST/GST
        </span>
      </div>
    </div>

    <div class="flex flex-col gap-6 mt-2 flex-grow">
      <div class="mx-auto w-full sm:w-48 h-32 shrink-0 border-2 border-dashed border-gray-700 rounded-xl flex items-center justify-center bg-gray-900/50 overflow-hidden">
        <div class="text-gray-600 text-[10px] text-center uppercase font-bold tracking-widest px-4">
           {{ isEditing ? 'Update Tax ID' : 'No Document on File' }}
        </div>
      </div>

      <div class="flex flex-col justify-top space-y-2 w-full flex-grow">
        <div class="space-y-1">
          <p class="text-sm flex justify-between gap-2">
            <span class="font-semibold dark:text-white">Tax ID:</span> 
            <span class="truncate text-right dark:text-white">{{ hstGst || '---' }}</span>
          </p>
        </div>

        <Transition
          enter-active-class="transition duration-300 ease-out"
          enter-from-class="transform -translate-y-2 opacity-0"
          enter-to-class="transform translate-y-0 opacity-100"
        >
          <div v-if="isEditing" class="pt-2 mt-2">
            <input 
              v-model="newTaxId"
              type="text"
              placeholder="Enter HST/GST Number"
              class="w-full bg-gray-50 rounded-lg px-3 py-2 text-sm outline-none focus:border-blue-500 text-black shadow-blue"
              @keyup.enter="handleAction"
            />
          </div>
        </Transition>
      </div>
      
      <div class="flex flex-col mt-auto">
        <button 
          @click="handleAction"
          :disabled="isSaving"
          class="w-full bg-blue-600 text-white font-semibold py-2.5 px-6 rounded-full hover:bg-blue-700 active:scale-95 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-blue-600/20 disabled:opacity-50"
        >
          <template v-if="isSaving">
            <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
          </template>
          <template v-else>
            <component :is="isEditing ? Send : Mail" class="w-4 h-4" />
            <span>{{ isEditing ? 'Send Request' : 'Request Tax Update' }}</span>
          </template>
        </button>
        <p class="text-[10px] text-gray-500 text-center uppercase tracking-widest mt-2">
          via Administration
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
        <div v-if="showToast" class="fixed top-6 right-6 z-[200] flex items-center gap-3 px-6 py-4 rounded-2xl border shadow-2xl min-w-[320px] backdrop-blur-md" :class="toastType === 'success' ? 'bg-green-600/90 border-green-400 text-white' : 'bg-red-600/90 border-red-400 text-white'">
          <CheckCircle2 v-if="toastType === 'success'" class="w-5 h-5 text-white" />
          <AlertCircle v-else class="w-5 h-5 text-white" />
          <span class="text-sm font-semibold">{{ toastMessage }}</span>
          <button @click="showToast = false" class="ml-auto p-1.5 hover:bg-white/10 rounded-full transition-colors">
            <X class="w-4 h-4" />
          </button>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>