import { defineNuxtRouteMiddleware, navigateTo } from "#app";
import { useAuth, useAuthToken } from "#imports";

export default defineNuxtRouteMiddleware(async () => {
  try {
    const { user, me } = useAuth();
    const authToken = useAuthToken();

    if (authToken.value && !user.value) {
      const response = await me();

      if (!response?.success) {
        authToken.value = null;
        return navigateTo("/login");
      }
    }

    if (!authToken.value) {
      return navigateTo("/login");
    }
  } catch {
    const authToken = useAuthToken();
    authToken.value = null;
    return navigateTo("/login");
  }
});
