<script setup lang="ts">
import { reactive, ref } from "vue";
import { Icon } from "@iconify/vue";
import { useRoute } from "vue-router";

interface RegisterResponse {
  success: boolean;
  message: string;
}

// Get token from param
const route = useRoute();
const token = route.query.token as string;
const email = route.query?.email as string;

const auth = useAuth();
const error = ref("");
const success = ref(false);
const loading = ref(false);
const message = ref("");

onMounted(() => {
  useRecaptcha().load();
});

const form = reactive({
  name: "",
  email: email,
  password: "",
  password_confirmation: "",
  token,
});

const submitForm = async () => {
  error.value = "";
  success.value = false;
  loading.value = true;
  message.value = "";

  const token = (await useRecaptcha().getToken("register")) ?? "";

  if (form.password != form.password_confirmation) {
    loading.value = false;
    return alert("The password confirmation does not match.");
  }

  try {
    const response: RegisterResponse = await auth.register(form, token);

    if (response?.success) {
      success.value = true;
      message.value = response.message;

      setTimeout(() => {
        navigateTo({
          path: "/login",
          query: {
            email: email,
          },
        });
      }, 3000);
    } else {
      error.value = response.message;
    }
  } catch (err: any) {
    error.value = err.status || "An error occurred";
  } finally {
    loading.value = false;
  }
};
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
  <form @submit.prevent="submitForm">
    <div
      class="h-screen flex items-center justify-center bg-gradient-to-r from-[#000235] to-[#1a1f6b] px-4"
    >
      <Card
        class="w-[380px] lg:bg-white/10 lg:backdrop-blur-xl p-8 rounded-2xl lg:shadow-xl animate-fadeIn lg:border border-white/20"
      >
        <img src="/assets/images/ydrive.png" alt="ydrive" class="h-auto" />

        <p class="text-gray-300 text-center lg:mt-1 mt-10 text-sm">
          Create an admin account
        </p>

        <Input
          type="text"
          v-model="form.name"
          placeholder="Name"
          class="mt-6 focus:ring-2 focus:ring-[#11A4FF] text-white"
          required
        />

        <Input
          type="email"
          v-model="form.email"
          class="mt-6 focus:ring-2 focus:ring-[#11A4FF] text-white"
          required
          readonly
        />

        <Input
          type="password"
          v-model="form.password"
          placeholder="Password"
          autocomplete="new-password"
          spellcheck="false"
          class="mt-6 focus:ring-2 focus:ring-[#11A4FF] text-white"
          required
        />

        <Input
          type="password"
          v-model="form.password_confirmation"
          placeholder="Password confirmation"
          autocomplete="new-password"
          spellcheck="false"
          class="mt-6 focus:ring-2 focus:ring-[#11A4FF] text-white"
          required
        />

        <Button
          class="w-full lg:mt-7 mt-16 bg-[#11A4FF] hover:bg-[#0e8bd8] transition-all duration-200 py-6 text-white rounded-full font-medium shadow-md hover:shadow-lg flex items-center justify-center gap-2 text-lg"
          type="submit"
        >
          <span v-if="!loading">Register</span>

          <Icon
            v-else
            icon="line-md:loading-loop"
            class="text-white text-2xl"
          />
        </Button>

        <p v-if="error" class="text-red-500 mt-2">
          {{
            error == "401"
              ? "Registration failed, please contact your developer."
              : "Registation failed"
          }}
        </p>
        <p v-if="success" class="text-green-500 mt-2">{{ message }}</p>

        <p class="text-gray-400 text-center text-xs lg:mt-4 mt-12">
          © {{ new Date().getFullYear() }} YDrive · All Rights Reserved
        </p>
      </Card>
    </div>
  </form>
</template>
