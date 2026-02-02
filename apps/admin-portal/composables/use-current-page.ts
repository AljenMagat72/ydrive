import { useCookie, useRuntimeConfig } from "#app";

export function useCurrentPage() {
  const config = useRuntimeConfig();

  return useCookie("current_page", {
    httpOnly: false,
    secure: config.public.environment === "production",
    sameSite: "strict",
    maxAge: 60 * 60 * 24 * 7,
  });
}
