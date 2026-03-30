This is a [Next.js](https://nextjs.org) project bootstrapped with [`create-next-app`](https://nextjs.org/docs/app/api-reference/cli/create-next-app).

## Getting Started

First, run the development server:

```bash
npm run dev
# or
yarn dev
# or
pnpm dev
# or
bun dev
```

Open [http://localhost:3000](http://localhost:3000) with your browser to see the result.

You can start editing the page by modifying `app/page.tsx`. The page auto-updates as you edit the file.

## Docker

From this directory:

```bash
docker compose up --build
```

Open **[http://localhost:5000](http://localhost:5000)** in your browser (see `ports` in `docker-compose.yaml`: host **5000** is forwarded to **3000** inside the container).

The container log line `Local: http://localhost:3000` is **normal**: that is the app’s address *inside* Docker. From your machine you always use the **left** port in the mapping (`5000:3000` → use `:5000`). If you see “port is already allocated”, change the host port (e.g. `5001:3000`) or run `docker compose down`. The image uses Next.js [standalone output](https://nextjs.org/docs/app/api-reference/config/next-config-js/output).

Build and run without Compose:

```bash
docker build -t chatwoot-ridehistory .
docker run --rm -p 5000:3000 chatwoot-ridehistory
```

This project uses [`next/font`](https://nextjs.org/docs/app/building-your-application/optimizing/fonts) to automatically optimize and load [Geist](https://vercel.com/font), a new font family for Vercel.

## Learn More

To learn more about Next.js, take a look at the following resources:

- [Next.js Documentation](https://nextjs.org/docs) - learn about Next.js features and API.
- [Learn Next.js](https://nextjs.org/learn) - an interactive Next.js tutorial.

You can check out [the Next.js GitHub repository](https://github.com/vercel/next.js) - your feedback and contributions are welcome!

## Deploy on Vercel

The easiest way to deploy your Next.js app is to use the [Vercel Platform](https://vercel.com/new?utm_medium=default-template&filter=next.js&utm_source=create-next-app&utm_campaign=create-next-app-readme) from the creators of Next.js.

Check out our [Next.js deployment documentation](https://nextjs.org/docs/app/building-your-application/deploying) for more details.
