<script setup lang="ts">
import { Head, NuxtImg } from '#components';
import { templateRef } from '@vueuse/core'
import Button from './ui/button/Button.vue';

const container = templateRef('container')
const { isVisible } = useScrollAnimation(container)

type ContentSide = 'left' | 'right'

type CTA = {
  link: string;
  title: string;
  target?: string;
}

withDefaults(
  defineProps<{
    image: string,
    imageAlt: string,
    title: string,
    body: string,
    side: ContentSide,
    cta?: CTA,
  }>(),
  {
    side: 'left',
  }
)
</script>

<template>
  <div
    ref="container"
    class="flex flex-col sm:items-center gap-4 sm:gap-12 transition-[transform, opacity] duration-700 ease-out"
    :class="[
      side === 'left' ? 'sm:flex-row' : 'sm:flex-row-reverse',
      isVisible
        ? 'opacity-100 translate-x-0'
        : side === 'left'
          ? 'opacity-0 -translate-x-16'
          : 'opacity-0 translate-x-16'
    ]"
  >
    <div class="flex-1">
      <NuxtImg
        :src="image"
        :width="632"
        :height="480"
        :alt="imageAlt"
        class="rounded-3xl w-full"
      />
    </div>
    <div class="flex flex-col flex-1 items-center sm:justify-center py-4 sm:py-0">
      <div>
        <h3 class="text-3xl">{{ title }}</h3>
        <p class="text-muted-foreground text-xl">{{ body }}</p>
      </div>
      <Button v-if="cta" class="mt-2 self-start p-6 text-xl" as-child>
        <a :href="cta.link" :target="cta.target">{{ cta.title }}</a>
      </Button>
    </div>
  </div>
</template>
