<script lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { dashboard } from '@/wayfinder/routes/driver';

export default {
  layout: AuthLayout,
};
</script>

<script setup lang="ts">
import { ref, h, watch, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { AxiosError } from 'axios';
import { toast } from 'vue-sonner';
import { useCountdown } from '@vueuse/core';

import { Button } from '@/components/ui/button';
import { Card, CardFooter, CardTitle, CardContent, CardHeader, CardDescription } from '@/components/ui/card';
import { Field, FieldError, FieldGroup, FieldLabel } from '@/components/ui/field';
import { InputOTP, InputOTPGroup, InputOTPSlot } from '@/components/ui/input-otp';

import PhoneInput, { type PhoneInputModel } from '@/components/input/PhoneInput.vue';
import { useLoginMutation } from '@/api/mutations/auth/use-login-mutation';
import { useVerifyMutation } from '@/api/mutations/auth/use-verify-mutation';

const { isPending: isPendingLogin, mutateAsync: login } = useLoginMutation();
const { isPending: isPendingVerify, mutateAsync: verify } = useVerifyMutation();

const step = ref<'login' | 'verify'>('login');
const transition = ref<'slide-left' | 'slide-right'>('slide-left');

const otp = ref('');
const errors = ref<Record<string, string[]>>();
const phoneNumber = ref<PhoneInputModel>();

const { reset, start, stop, remaining } = useCountdown(60, { immediate: false });

const formattedPhoneNumber = computed(() => {
  return `+${phoneNumber.value?.countryCallingCode}${phoneNumber.value?.nationalNumber}`;
});

watch(step, (next, prev) => {
  transition.value = prev === 'login' && next === 'verify' ? 'slide-left' : 'slide-right';
});

async function submitLogin() {
  errors.value = undefined;

  try {
    await login({
      phoneNumber: formattedPhoneNumber.value,
    });

    step.value = 'verify';
    reset();
    start();
  } catch (e) {
    handleError(e);
  }
}

async function submitVerify() {
  errors.value = undefined;

  try {
    await verify({
      code: otp.value,
      phoneNumber: formattedPhoneNumber.value,
    });

    router.visit(dashboard.url());
  } catch (e) {
    handleError(e);
  }
}

async function resendCode() {
  if (remaining.value > 0) return;

  try {
    await login({
      phoneNumber: formattedPhoneNumber.value,
    });

    reset();
    start();
    toast.success('OTP code resent');
  } catch (e) {
    handleError(e);
  }
}

function handleError(e: unknown) {
  if (e instanceof AxiosError) {
    if (e.response?.status === 422) {
      errors.value = e.response?.data.errors;
    }
    else {
      toast.error('Something went wrong', {
        closeButton: true,
        description: h(
          'a',
          {
            href: 'mailto:helpme@ydriveapp.com',
            class: 'underline text-sm',
            onClick: (ev: Event) => ev.stopPropagation(),
          },
          'Contact support'
        ),
      });
    }
  }
}

function back() {
  stop();
  step.value = 'login';
  otp.value = '';
}
</script>

<template>
  <Head title="Driver | Login" />
  <div class="max-w-xs w-full">
    <Transition
      :name="transition"
      mode="out-in"
    >
      <form
        v-if="step === 'login'"
        key="login"
        @submit.prevent="submitLogin"
      >
        <Card>
          <CardHeader>
            <CardTitle>Login</CardTitle>
          </CardHeader>

          <CardContent>
            <FieldGroup>
              <Field>
                <FieldLabel>Phone Number</FieldLabel>
                <PhoneInput
                  v-model="phoneNumber"
                  :disabled="isPendingLogin"
                  @update:model-value="() => (errors = undefined)"
                />
                <FieldError v-if="errors?.phoneNumber">Please enter a valid phone number</FieldError>
              </Field>
            </FieldGroup>
          </CardContent>

          <CardFooter class="justify-end">
            <Button
              :disabled="isPendingLogin"
              type="submit"
            >
              Login
            </Button>
          </CardFooter>
        </Card>
      </form>

      <form
        v-else
        key="verify"
        @submit.prevent="submitVerify"
      >
        <Card>
          <CardHeader>
            <CardTitle>Verify Code</CardTitle>
          </CardHeader>

          <CardContent>
            <CardDescription class="mb-2">
              Enter the 6-digit code sent to {{ formattedPhoneNumber }}
            </CardDescription>

            <FieldGroup>
              <Field>
                <div class="flex justify-center mb-2">
                  <InputOTP
                    v-model="otp"
                    :maxlength="6"
                  >
                    <InputOTPGroup>
                      <InputOTPSlot
                        v-for="i in 6"
                        :key="i"
                        :index="i - 1"
                      />
                    </InputOTPGroup>
                  </InputOTP>
                </div>
                <FieldError v-if="errors?.otp">Invalid code</FieldError>
              </Field>
            </FieldGroup>

            <Button
              variant="ghost"
              class="w-full text-center mt-2"
              :disabled="remaining > 0"
              @click.prevent="resendCode"
            >
              <span v-if="remaining > 0">Resend in {{ remaining }}s</span>
              <span v-else>Resend Code</span>
            </Button>
          </CardContent>

          <CardFooter class="justify-between">
            <Button
              variant="secondary"
              type="button"
              @click="back"
            >
              Back
            </Button>
            <Button
              :disabled="isPendingVerify"
              type="submit"
            >
              Verify
            </Button>
          </CardFooter>
        </Card>
      </form>
    </Transition>
  </div>
</template>

<style scoped>
.slide-left-enter-active,
.slide-left-leave-active,
.slide-right-enter-active,
.slide-right-leave-active {
  transition: transform 0.3s ease-out, opacity 0.3s ease-out;
}

.slide-left-enter-from {
  transform: translateX(100%);
  opacity: 0;
}

.slide-left-enter-to {
  transform: translateX(0);
  opacity: 1;
}

.slide-left-leave-from {
  transform: translateX(0);
  opacity: 1;
}

.slide-left-leave-to {
  transform: translateX(-100%);
  opacity: 0;
}

.slide-right-enter-from {
  transform: translateX(-100%);
  opacity: 0;
}

.slide-right-enter-to {
  transform: translateX(0);
  opacity: 1;
}

.slide-right-leave-from {
  transform: translateX(0);
  opacity: 1;
}

.slide-right-leave-to {
  transform: translateX(100%);
  opacity: 0;
}
</style>
