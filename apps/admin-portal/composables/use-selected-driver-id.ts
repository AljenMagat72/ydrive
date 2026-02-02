import { useCookie, useRuntimeConfig } from "#app";

export function useSelectedDriverId() {
  const config = useRuntimeConfig();

  return useCookie("selected_driver_id", {
    httpOnly: false,
    secure: config.public.environment === "production",
    sameSite: "strict",
    maxAge: 60 * 60 * 24 * 7,
  });
}
