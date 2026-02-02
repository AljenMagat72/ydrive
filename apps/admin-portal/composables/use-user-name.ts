import { useCookie, useRuntimeConfig } from "#app";

export function useUserName() {
  const config = useRuntimeConfig();

  return useCookie("user_name", {
    httpOnly: false,
    secure: config.public.environment === "production",
    sameSite: "strict",
    maxAge: 60 * 60 * 24 * 7,
  });
}
