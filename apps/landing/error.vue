<script setup lang="ts">
import type { NuxtError } from '#app';

import Button from './components/ui/button/Button.vue';

const props = defineProps({
  error: Object as () => NuxtError
})
</script>

<template>
  <div class="lg:container mx-auto font-[sriracha] px-2 flex flex-col min-h-screen">
    <Header />
    <main class="flex flex-col flex-grow items-center justify-center gap-4">
      <h1 class="text-4xl">{{ error?.statusCode }}</h1>
      <p>{{ error?.statusMessage }}</p>
      <Button as-child>
        <NuxtLink to="/">Go back home</NuxtLink>
      </Button>
    </main>
    <Footer />
  </div>
</template>