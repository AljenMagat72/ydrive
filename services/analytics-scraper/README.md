# driver-analytics-scraper

There's no way of accessing a drivers acceptance statistics over a seven day period through the api.
This just scrapes their reports page and stores it in redis

Set environment variables:

USERNAME=autofleet username
PASSWORD=autofleet password

REDIS_URL=http://localhost:6379

To install dependencies:

```bash
bun install
```

To run:

```bash
bun run start
```


