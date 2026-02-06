<script setup lang="ts">
import { Button } from '~/components/ui/button';

useSeoMeta({
  title: 'HOME | Y Drive',
  ogTitle: 'HOME | Y Drive',
  ogUrl: 'https://www.ydriveapp.com',
  ogSiteName: 'Y Drive',
  ogType: 'website',
  twitterCard: 'summary_large_image',
  twitterTitle: 'HOME | Y Drive',
});

const contents = [{
  image: "/client-ordering.webp",
  imageAlt: 'Rider displaying the app',
  title: "Ride Smarter with the Y Drive App",
  body: "Getting started with Y Drive is quick and easy—download the app and create your profile in just minutes. Our user-friendly interface makes booking a breeze, with transparent pricing and informative trip details that give you peace of mind every step of the way.",
  cta: {
    link: 'https://0mma3.app.link/D5dlnpmDaVb',
    title: 'Download Now',
    target: '_blank'
  }
}, {
  image: "/become-a-driver.webp",
  imageAlt: 'Y Driver displaying the app',
  title: "Earn on your schedule, in your city.",
  body: "Join our growing team of community-first drivers and take control of your time and income. Whether you’re driving full-time or part-time, we provide the tools, support, and rider volume to help you thrive. Safe, flexible, and built around you.",
  cta: {
    link: '/driver',
    title: 'Sign Up',
  }
}]
</script>

<template>
  <div class="lg:container mx-auto font-[sriracha]">
    <Header class="px-2" />
    <section class="min-h-[720px] overflow-hidden relative">
      <RideBooker />
    </section>
    <section class="px-2 mt-4 overflow-x-hidden">
      <ul class="space-y-4">
        <li
          v-for="(content, index) in contents"
          :key="index"
        >
          <ContentSection
            :image="content.image"
            :image-alt="content.imageAlt"
            :title="content.title"
            :body="content.body"
            :side="index % 2 === 0 ? 'left' : 'right'"
            :cta="content.cta"
          />
        </li>
      </ul>
    </section>
    <section class="mt-4 py-16 px-2 text-center">
      <div class="text-3xl mb-4">
        <h3>Book a ride.</h3>
        <h4 class="text-muted-foreground">Seamless booking rideshare.</h4>
      </div>
      <Button
        as-child
        class="p-6 text-xl"
      >
        <a
          href="https://0mma3.app.link/D5dlnpmDaVb"
          target="_blank"
        >
          Download Now
        </a>
      </Button>
    </section>
    <Footer class="px-2" />
  </div>
</template>
