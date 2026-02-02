import { useCookie, useRuntimeConfig } from "#app";

export function useCurrentSubPage() {
  const config = useRuntimeConfig();

  return useCookie("current_sub_page", {
    httpOnly: false,
    secure: config.public.environment === "production",
    sameSite: "strict",
    maxAge: 60 * 60 * 24 * 7,
  });
}
