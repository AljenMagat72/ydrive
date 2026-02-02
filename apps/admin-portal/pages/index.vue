<script setup lang="ts">
const auth = useAuth();

onBeforeMount(() => {
  if (auth.isUserLoggedIn) {
    navigateTo("/admin/dashboard");
  }
});
</script>

<template>
  <div class="h-screen grid place-items-center">
    <h1>YDrive Homepage</h1>
  </div>
</template>
