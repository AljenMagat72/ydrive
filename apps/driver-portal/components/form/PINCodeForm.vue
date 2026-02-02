<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useCountdown } from '@vueuse/core'
import { AlertCircle } from 'lucide-vue-next';
import { useAuth, useRecaptcha } from '#imports';

import { Card, CardContent, CardHeader, CardFooter, CardTitle, CardDescription } from '../ui/card';
import { Button } from '~/components/ui/button';
import CodeInput from '~/components/input/CodeInput.vue';
import LoadingButton from '../LoadingButton.vue';
import { Label } from '../ui/label';


const { remaining, start } = useCountdown(60);
const { verify, resend } = useAuth();

const code = ref<string>('');
const isSubmitting = ref<boolean>(false);
const pincodeError = ref<string | null>(null);

const emit = defineEmits(['next', 'prev']);

async function handleSubmit() {
  isSubmitting.value = true;
  pincodeError.value = null;
  
  try {
    const token = await useRecaptcha().getToken('verify');
    if (!token) {
      pincodeError.value = "Security verification failed. Please refresh.";
      isSubmitting.value = false;
      return;
    }

    const resp = await verify(code.value, token);

    if (resp && resp.success) {
      emit('next');
    }else {
      pincodeError.value = resp?.message || "Invalid code. Please try again.";
    }
  } catch (e: any) {
      pincodeError.value = e.response?._data?.message || "Connection lost. Please try again.";
  } finally {
      isSubmitting.value = false;
}
}

async function handleResend() {
  pincodeError.value = null;
  await resend();
  remaining.value = 60;
  start();
}

function handleBack() {
  emit('prev');
}

onMounted(() => {
  start();
});
</script>

<template>
  <form @submit.prevent="handleSubmit" class="shadow-blue rounded-xl">
    <Card>
      <CardHeader>
        <CardTitle>Code sent!</CardTitle>
        <CardDescription>Enter the code we sent to your phone number</CardDescription>
      </CardHeader>
      <CardContent>
        <div class="space-y-2">
          <Label for="pin">OTP</Label>
          <CodeInput
            v-model="code"
            :length="6"
          />
        </div>
        <Button
          class="text-xs p-0"
          :disabled="remaining !== 0"
          variant="link"
          type="button"
          @click="handleResend"
        >
          <span v-if="remaining !== 0">Resend code in {{ remaining }}s</span>
          <span v-else>Resend code</span>
        </Button>
        <div 
          v-if="pincodeError" 
          class="flex items-center justify-center gap-.5 font-semibold text-center rounded-xl bg-white text-destructive border-2 border-destructive/30 shadow-sm animate-in fade-in zoom-in duration-300"
        >
          <div class="flex items-center justify-center size-6 rounded-full bg-destructive/10">
            <AlertCircle class="size-4" />
          </div>
          
          <span class="text-sm tracking-tight">
            {{ pincodeError }}
          </span>
        </div>
      </CardContent>
      <CardFooter class="justify-between">
        <Button
          type="button"
          @click="handleBack"
        >
          Back
        </Button>
        <LoadingButton
          type="submit"
          :disabled="code?.length !== 6"
          :updating="isSubmitting"
        >
          Submit
        </LoadingButton>
      </CardFooter>
    </Card>
  </form>
</template>