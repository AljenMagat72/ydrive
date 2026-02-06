export default defineNuxtPlugin(() => {
  if (import.meta.client) {
    const BASE_URL = "https://coms.ydriveapp.com"

    const script = document.createElement("script")
    script.src = `${BASE_URL}/packs/js/sdk.js`
    script.async = true

    script.onload = () => {
      // @ts-ignore
      window.chatwootSDK?.run({
        websiteToken: "gnYqkGj2BfeUMXU4EVR5tqcd",
        baseUrl: BASE_URL
      })
    }

    document.body.appendChild(script)
  }
})
