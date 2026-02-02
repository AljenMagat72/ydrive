import { defineNuxtRouteMiddleware, navigateTo } from "#app";
import { useAuthToken } from "#imports";

export default defineNuxtRouteMiddleware((to) => {
  const authToken = useAuthToken();

  // Allow access to login page
  if (to.path === "/login") {
    return;
  }

  if (!authToken.value) {
    return navigateTo("/login");
  }
});
