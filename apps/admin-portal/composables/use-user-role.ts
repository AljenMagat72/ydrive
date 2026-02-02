import { useCookie, useRuntimeConfig } from "#app";

export function useUserRole() {
  const config = useRuntimeConfig();

  return useCookie("user_role", {
    httpOnly: false,
    secure: config.public.environment === "production",
    sameSite: "strict",
    maxAge: 60 * 60 * 24 * 7,
  });
}
