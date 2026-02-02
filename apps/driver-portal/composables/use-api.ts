import { useRequestEvent } from "#app";

export function useAPI() {

  async function api(url: string, options: {}) {
    const headers = options.headers || {};

    if (import.meta.server) {
      const event = useRequestEvent();
      if (event?.node.req.headers.cookie) {
        headers.cookie = event.node.req.headers.cookie;
      }
    }

    return await $fetch(url, {
      ...options,
      headers,
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
