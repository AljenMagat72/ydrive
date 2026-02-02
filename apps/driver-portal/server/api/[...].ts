import { defineEventHandler, getCookie, getHeaders, proxyRequest } from '#imports'

export default defineEventHandler(async (event) => {
  const path = event.node.req.url

  const authToken = getCookie(event, 'auth_token');
  const headers = {
    ...getHeaders(event),
  }

  if (authToken && !(headers.Authorization || headers.authorization)) {
    headers.Authorization = `Bearer ${authToken}`;
  }

  headers['X-Forwarded-For'] = event.node.req.socket.remoteAddress;

  return await proxyRequest(event, `${process.env.YDRIVE_API}${path}`, {
    headers,
  });
});