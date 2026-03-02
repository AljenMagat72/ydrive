import { useRequestEvent } from "#app";

export function useAPI() {
  const config = useRuntimeConfig();
  const baseURL = config.public.apiBase;
  const authToken = useAuthToken();

  async function api(url: string, options: {}) {
    const headers = {
      'Accept': 'application/json',
      ...options.headers,
    };

    if (authToken.value) {
      headers['Authorization'] = `Bearer ${authToken.value}`;
    }

    if (import.meta.server) {
      const event = useRequestEvent();
      if (event?.node.req.headers.cookie) {
        headers.cookie = event.node.req.headers.cookie;
      }
    }

    return await $fetch(url, {
      baseURL,
      ...options,
      headers,
      credentials: 'include',
    });
  }

  function post<T>(
    url: string,
    body: object = {},
    headers?: Record<string, string>
  ) {
    return api(url, {
      method: "POST",
      body,
      ...(headers && { headers }),
    }) as Promise<T>;
  }

  function del<T>(
    url: string,
    query: object = {},
    headers?: Record<string, string>
  ) {
    return api(url, {
      method: "delete",
      query,
      ...(headers && { headers }),
    }) as Promise<T>;
  }

  function get<T>(
    url: string,
    query: object = {},
    headers?: Record<string, string>
  ) {
    return api(url, {
      method: "GET",
      query,
      ...(headers && { headers }),
    }) as Promise<T>;
  }

  return {
    post,
    get,
    del,
  };
}
