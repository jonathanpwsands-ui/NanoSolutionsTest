import { test as base } from '@hyvor/laravel-playwright';
import { expect } from '@playwright/test';

export const test = base.extend<{
  user: { email: string };
}>({
  user: async ({}, use) => {
    await use({ email: 'test@example.com' });
  },
});

export { expect };
