<script setup lang="ts">
import { Label } from '~/components/ui/label';
import PhoneInput from '~/components/input/PhoneInput.vue';
import { Card, CardContent, CardHeader, CardFooter, CardTitle, CardDescription } from '../ui/card';
import { ref } from 'vue';
import { useAuth, useRecaptcha } from '#imports';
import LoadingButton from '../LoadingButton.vue';
import { AlertCircle } from 'lucide-vue-next';

const emit = defineEmits(['next']);

const isSubmitting = ref(false);

const { login } = useAuth();

const phoneNumber = ref<{ isValid: boolean, countryCallingCode: string, nationalNumber: string }>({
  isValid: false,
  countryCallingCode: '',
  nationalNumber: '',
});

const loginError = ref<string | null>(null);

async function submitPhoneNumber() {
  isSubmitting.value = true;
  loginError.value = null;

    try{
    const token = await useRecaptcha().getToken('register'); 
    
    if (!token){
      loginError.value = "Security verification failed. Please refresh.";
      isSubmitting.value = false;
      return;
    }

    const resp = await login(`${phoneNumber.value.countryCallingCode}${phoneNumber.value.nationalNumber}`, token);
    
    if (resp && resp.success) {
      emit('next');  
    } else {
      loginError.value = resp?.message || "Invalid phone number or account not found.";
    }
  } catch (e: any) {
    console.error("Login Error:", e);
    loginError.value = "Invalid Phone Number";
  } finally {
    isSubmitting.value = false;
  }
}
</script>

<template>
  <form @submit.prevent="submitPhoneNumber" class="shadow-blue rounded-xl">
    <Card>
      <CardHeader>
        <CardTitle>Login</CardTitle>
        <CardDescription>Enter your phone number to login</CardDescription>
      </CardHeader>
      <CardContent class="space-y-2">
        <div class="space-y-2">
          <Label for="phone-number">Phone Number</Label>
          <PhoneInput
            id="phone-number"
            v-model="phoneNumber"
          />
        </div>
        <div 
          v-if="loginError" 
          class="flex items-center justify-center gap-.5 font-semibold text-center rounded-lg bg-white text-destructive border-2 border-destructive/30 shadow-sm animate-in fade-in zoom-in duration-300"
        >
          <div class="flex items-center justify-center size-6 rounded-full bg-destructive/10">
            <AlertCircle class="size-4" />
          </div>
          
          <span class="text-sm tracking-tight">
            {{ loginError }}
          </span>
        </div>
      </CardContent>
      <CardFooter>
        <LoadingButton
          class="ml-auto"
          type="submit"
          :disabled="!phoneNumber.isValid"
          :updating="isSubmitting"
        >
          Login
        </LoadingButton>
      </CardFooter>
    </Card>
  </form>
</template>