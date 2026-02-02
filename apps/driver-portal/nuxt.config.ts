import tailwindcss from "@tailwindcss/vite";

// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: "2025-05-15",
  devtools: { enabled: true },

  imports: {
    autoImport: false,
  },

  modules: [
    '@nuxt/eslint',
    '@nuxt/icon',
    '@nuxt/test-utils',
    '@nuxt/image',
    'shadcn-nuxt',
    '@nuxtjs/color-mode',
    '@sentry/nuxt/module'
  ],

  css: ['~/assets/css/tailwind.css'],

  vite: {
    plugins: [tailwindcss()],
  },

  ssr: false,

  components: {
    dirs: [],
  },

  app: {
    head: {
      title: "Driver Portal",
    },
  },

  runtimeConfig: {
    chartKey: process.env.CHART_KEY,
  },

  sentry: {
    org: 'ydriveapp',
    project: 'driver-portal'
  },

  sourcemap: {
    client: 'hidden'
  }
})
