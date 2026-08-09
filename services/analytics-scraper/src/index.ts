import { redis } from 'bun';
import Baker from 'cronbake';

import { scrape } from './scraper';

const CACHE_KEY = 'driver_analytics';

const baker = Baker.create();

baker.add({
  name: 'scraper-job',
  cron: '@every_4_hours',
  immediate: true,
  callback: async () => {
    const analytics = await scrape();
    await redis.set(CACHE_KEY, JSON.stringify(analytics));    
  },
});

baker.bake('scraper-job');

Bun.serve({
  port: 8080,
  routes: {
    '/api/scrape': async () => {
      try {
        const cached = await redis.get(CACHE_KEY);

        if (!cached) {
          return new Response(JSON.stringify({ error: 'No data in cache' }), {
            status: 404,
            headers: { "Content-Type": "application/json" },
          });
        }

        return new Response(cached, {
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