import {
  defineEventHandler,
  getCookie,
  getHeaders,
  proxyRequest,
} from "#imports";

export default defineEventHandler(async (event) => {
  // Strip `/api` prefix
  const originalUrl = event.node.req.url || "/";
  const path = originalUrl.replace(/^\/api/, "");

  const authToken = getCookie(event, "auth_token");
  const headers = {
    ...getHeaders(event),
  };

  // Bearer token
  if (authToken) {
    headers.Authorization = `Bearer ${authToken}`;
  }

  // Client IP (optional)
  const ip = event.node.req.socket.remoteAddress;
  if (ip) {
    headers["X-Forwarded-For"] = ip;
  }

  //Proxy to backend (port 8000)
  return proxyRequest(event, `${process.env.YDRIVE_API}${path}`, {
    headers,
  });
});
