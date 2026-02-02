import * as Sentry from "@sentry/nuxt";

Sentry.init({
  enabled: process.env.NODE_ENV === 'production',

  dsn: "https://99a14e42d39145adac057edb54d55f81@o4510160426237952.ingest.us.sentry.io/4510672993779712",

  // We recommend adjusting this value in production, or using tracesSampler
  // for finer control
  tracesSampleRate: 1.0,

  // Enable logs to be sent to Sentry
  enableLogs: true,

  // Enable sending of user PII (Personally Identifiable Information)
  // https://docs.sentry.io/platforms/javascript/guides/nuxt/configuration/options/#sendDefaultPii
  sendDefaultPii: true,

  // Setting this option to true will print useful information to the console while you're setting up Sentry.
  debug: false,
});
