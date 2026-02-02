import { defineNuxtRouteMiddleware, createError, useRuntimeConfig } from '#app';

export default defineNuxtRouteMiddleware(async (to) => {
  if (!import.meta.client) {
    const config = useRuntimeConfig();
    const expectedKey = config.chartKey;

    const providedKey = to.query.key;

    if (!providedKey || providedKey !== expectedKey) {
      throw createError({
        statusCode: 404,
        statusMessage: 'Not found'
      });
    }
  }
});