// tests/playwright/fixtures.ts
import { test as base } from '@hyvor/laravel-playwright';

export const test = base.extend<{
  user: { email: string };
}>({
  user: async ({}, use) => {
    await use({ email: 'test@example.com' });
  },
});
