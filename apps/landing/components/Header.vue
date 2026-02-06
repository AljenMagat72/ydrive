<script setup lang="ts">
import { ArrowUpRight, ChevronDown } from 'lucide-vue-next';
import { onClickOutside, templateRef } from '@vueuse/core';

const container = templateRef<HTMLButtonElement>('container');

onClickOutside(container, close);

defineOptions({
  inheritAttrs: false,
});

const links = [
  {
    text: 'Home',
    href: '/',
    target: '_self',
  },
  {
    text: 'Drivers',
    children: [
      {
        text: 'Sign Up',
        href: '/driver',
        target: '_self',
      },
      {
        text: 'Driver Portal',
        href: 'https://driver.ydriveapp.com',
        target: '_blank',
      },
    ]
  },
];

const isOpen = ref(false);

const isSubMenuOpen = ref(false);

function close() {
  isSubMenuOpen.value = false;
}

function open() {
  isSubMenuOpen.value = true;
}

function toggle() {
  isSubMenuOpen.value = !isSubMenuOpen.value;
}

onMounted(() => {
  window.addEventListener('blur', close);
});

onUnmounted(() => {
  window.removeEventListener('blur', close);
});

</script>

<template>
  <header class="sticky top-0 z-30 bg-background">
    <div class="px-2 md:hidden flex items-center relative z-20">
      <a href="/">
        <NuxtImg
          :height="48"
          alt="Y Drive Logo"
          src="/y-drive-logo.avif"
        />
      </a>
      <Hamburger
        v-model="isOpen"
        class="ml-auto"
      />
    </div>
    <div
      class="bg-background w-full h-dvh gap-4 flex flex-col fixed top-0 z-10 pt-[48px] md:pt-0 transition-transform md:relative md:grid md:grid-cols-[minmax(max-content,1fr)_auto_minmax(max-content,1fr)] md:items-center md:w-auto md:h-auto"
      v-bind="$attrs"
      :class="[
        { 'translate-x-[0%] md:translate-x-0': isOpen },
        { 'translate-x-[100%] md:translate-x-0': !isOpen }
      ]"
    >
      <a href="/">
        <NuxtImg
          class="hidden md:block"
          :height="48"
          alt="Y Drive Logo"
          src="/y-drive-logo.avif"
        />
      </a>
      <ul
        ref="container"
        class="flex flex-col gap-x-4 text-xl justify-center md:flex-row"
      >
        <li
          v-for="link in links"
          :key="link.text"
          class="p-2 w-fit"
          :class="{ 'group': link.children }"
        >
          <a
            v-if="link.href"
            :href="link.href"
            :target="link.target"
          >
            {{ link.text }}
          </a>
          <button
            class="cursor-pointer flex items-center gap-x-2"
            @click="toggle"
            :aria-expanded="isSubMenuOpen"
            aria-controls="sub-menu"
            aria-label="Toggle sub menu"
            v-if="link.children"
          >
            {{ link.text }}
            <ChevronDown
              :size="18"
              class="transition-transform"
              :class="{ 'rotate-180': isSubMenuOpen }"
            />
          </button>
          <Transition
            name="fade"
            appear
          >
            <ul
              id="sub-menu"
              v-if="link.children && isSubMenuOpen"
              key="submenu"
              class="flex flex-col w-full md:flex-row md:justify-between md:text-white md:bg-black md:absolute md:top-full md:left-0 md:right-0 md:m-auto md:z-10 md:text-center"
            >
              <li
                v-for="childLink in link.children"
                class="flex-1 p-2"
              >
                <a
                  :href="childLink.href"
                  :target="childLink.target"
                >
                  {{ childLink.text }}
                </a>
              </li>
            </ul>
          </Transition>
        </li>
      </ul>
      <a
        class="group p-2 gap-x-2 justify-self-end text-xl flex flex-row items-center animate-scale-pulse"
        href="https://0mma3.app.link/D5dlnpmDaVb"
        target="_blank"
      >
        <div class="rounded bg-primary p-1">
          <ArrowUpRight
            class="group-hover:rotate-450 text-white transition-transform"
            :size="16"
          />
        </div> Download
      </a>
    </div>
  </header>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition-duration: 300ms
}

.fade-enter-from {
  opacity: 0;
}

.fade-enter-to {
  opacity: 100;
}

.fade-leave-from {
  opacity: 100;
}

.fade-leave-to {
  opacity: 0;
}
</style>