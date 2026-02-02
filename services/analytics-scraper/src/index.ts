import { scrape, type DriverAnalytics } from './scraper';

let promise: Promise<DriverAnalytics[]> | undefined;

Bun.serve({
  port: 8080,
  idleTimeout: 255,
  routes: {
    '/api/scrape': async () => {
      try {
        if (!promise) {
          promise = scrape().finally(() => {
            promise = undefined;
          });
        }

        const drivers = await promise;

        return new Response(JSON.stringify(drivers), {
          status: 200,
          headers: { "Content-Type": "application/json" },
        });
      } catch (err) {
        const message = err instanceof Error ? err.message : 'unknown error';
        return new Response(JSON.stringify({ error: message }), {
          status: 500,
          headers: { "Content-Type": "application/json" },
        });
      }
    },
  },

  fetch(req) {
    return new Response('Not Found', { status: 404 });
  },
});
