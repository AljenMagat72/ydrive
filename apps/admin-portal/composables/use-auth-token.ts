import { useCookie, useRuntimeConfig } from '#app';

export function useAuthToken() {
  const config = useRuntimeConfig();

  return useCookie('auth_token', {
    httpOnly: false,
    secure: config.public.environment === 'production',
    sameSite: 'strict',
    maxAge: 60 * 60 * 24 * 7
  });
}