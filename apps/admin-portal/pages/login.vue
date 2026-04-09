<script setup lang="ts">
import { Icon } from "@iconify/vue";
import { Eye, EyeClosed } from "lucide-vue-next";

const auth = useAuth();

const showPassword = ref(false);

onMounted(() => {
  useRecaptcha().load();
});

onBeforeMount(() => {
  if (auth.isUserLoggedIn) {
    navigateTo("/admin/dashboard");
  }
});

interface LoginResponse {
  success: boolean;
  message?: string;
  user?: any;
}

// Get email if we have a url param
const route = useRoute();
const emailFromUrl = (route.query.email as string) ?? "";

const email = ref(emailFromUrl);
const password = ref("");
const error = ref("");
const loading = ref(false);

// Submit login
async function submit() {
  loading.value = true;
  error.value = "";

  const token = (await useRecaptcha().getToken("register")) ?? "";

  try {
    const response: LoginResponse = await auth.login(
      email.value,
      password.value,
      token,
    );

    if (response.success) {
      navigateTo("/admin/dashboard");
    } else {
      error.value = response.message || "Invalid credentials";
    }
  } catch (err: unknown) {
    if (err instanceof Error) {
      error.value = err.message;
    } else {
      error.value = "An unexpected error occurred";
    }
  } finally {
    loading.value = false;
  }
}
</script>

<!-- Animations -->
<style>
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-fadeIn {
  animation: fadeIn 0.5s ease-out;
}
</style>

<template>
  <form @submit.prevent="submit">
    <div
      class="h-screen flex items-center justify-center bg-gradient-to-r from-[#000235] to-[#1a1f6b] px-0"
    >
      <Card
        class="w-[380px] lg:bg-white/10 lg:backdrop-blur-xl p-8 rounded-2xl lg:shadow-xl animate-fadeIn lg:border lg:border-white/20"
      >
        <img src="/assets/images/ydrive.png" alt="ydrive" class="h-auto" />

        <p class="text-gray-300 text-center mt-1 lg:text-sm">
          Welcome back admin👋
        </p>

        <Input
          type="email"
          v-model="email"
          placeholder="Email"
          class="lg:mt-6 mt-12 focus:ring-2 focus:ring-[#11A4FF] text-white text-center"
          required
          :readonly="emailFromUrl !== ''"
        />

        <div class="relative mt-3">
          <Input
            :type="showPassword ? 'text' : 'password'"
            v-model="password"
            placeholder="Password"
            autocomplete="current-password"
            spellcheck="false"
            class="w-full pr-10 focus:ring-2 focus:ring-[#11A4FF] text-white text-center"
            required
          />

          <button
            type="button"
            class="absolute inset-y-0 right-3 flex items-center text-white"
            @click="showPassword = !showPassword"
          >
            <Eye v-if="showPassword" />
            <EyeClosed v-else />
          </button>
        </div>

        <Button
          class="w-full lg:mt-5 mt-10 bg-[#11A4FF] hover:bg-[#0e8bd8] transition-all duration-200 py-7 text-white rounded-full font-medium shadow-md hover:shadow-lg flex items-center justify-center gap-2"
          type="submit"
        >
          <span v-if="!loading" class="text-lg">Log In</span>

          <Icon
            v-else
            icon="line-md:loading-loop"
            class="text-white text-2xl"
          />
        </Button>

        <p class="text-red-400 text-center text-sm mt-3 min-h-[20px]">
          {{ error }}
        </p>

        <p class="text-gray-400 text-center text-sm mt-4">
          © {{ new Date().getFullYear() }} YDrive · All Rights Reserved
        </p>
      </Card>
    </div>
  </form>
</template>
