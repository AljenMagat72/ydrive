import tailwindcss from "@tailwindcss/vite";

export default defineNuxtConfig({
  compatibilityDate: "2025-07-15",
  devtools: { enabled: false },
  css: ["./assets/css/main.css"],
  runtimeConfig: {
    public: {
      apiBase: process.env.YDRIVE_API,
    },
  },
  vite: {
    plugins: [tailwindcss()],
  },
  modules: ["shadcn-nuxt", "nuxt-lucide-icons", "@nuxtjs/color-mode"],
  colorMode: {
    preference: "light",
    fallback: "light",
    classSuffix: "",
  },
  shadcn: {
    componentDir: "./components/ui",
  },
  app: {
    head: {
      title: "YDRIVE Admin Portal",
    },
  },
});
