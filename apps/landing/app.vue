<script setup>
useHead({
  script: [
    {
      src: 'https://www.googletagmanager.com/gtag/js?id=AW-469369520',
      async: true
    },
    {
      children: `
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'AW-469369520');
      `
    },
    {
      children: `
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-M6XBBZ9');
      `
    },
  ]
})
</script>


<template>
  <noscript>
    <iframe
      src="https://www.googletagmanager.com/ns.html?id=GTM-M6XBBZ9"
      height="0"
      width="0"
      style="display:none;visibility:hidden"
    />
  </noscript>
  <main>
    <NuxtPage />
  </main>
</template>
