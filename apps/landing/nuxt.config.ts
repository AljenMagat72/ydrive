import tailwindcss from '@tailwindcss/vite';

// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-05-15',
  devtools: { enabled: true },
  plugins: ['~/plugins/crisp.client.ts'],
  modules: [
    'shadcn-nuxt',
    '@nuxtjs/color-mode',
    ['@nuxtjs/google-fonts', {
      families: {
        Sriracha: true,
      }
    }],
    '@nuxt/image',
  ],
  css: ['~/assets/css/tailwind.css'],
  vite: {
    plugins: [
      tailwindcss(),
    ],
  },
  app: {
    head: {
      meta: [
        {
          name: 'description',
          content: "Y Drive is Canada's Premier Ride Share Service - Taking the model made famous by Uber and Lyft and combining it with the service quality and expectations of a private service. With a constantly growing list of cities, download the app and give us a try!"
        },
        {
          property: 'og:image',
          content: 'https://ydriveapp.com/become-a-driver.webp'
        },
        {
          name: 'twitter:image',
          content: 'https://ydriveapp.com/become-a-driver.webp'
        }
      ]
    }
  }
})