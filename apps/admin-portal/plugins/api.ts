import axios from "axios";

export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig();

  const apiClient = axios.create({
    baseURL: config.public.apiBase,
    timeout: 5000,
    headers: {
      "Content-Type": "application/json",
    },
  });

  return {
    provide: {
      api: apiClient,
    },
  };
});
