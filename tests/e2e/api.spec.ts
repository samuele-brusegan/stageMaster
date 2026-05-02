import { test, expect } from '@playwright/test';

test.describe('API Endpoints', () => {
  test.skip('should return talent list from API', async ({ request }) => {
    // API endpoints are tested by PHPUnit - E2E tests focus on UI interactions
    // This test is skipped to avoid database dependency in E2E tests
  });

  test.skip('should return screens from API', async ({ request }) => {
    // API endpoints are tested by PHPUnit - E2E tests focus on UI interactions
  });

  test.skip('should return queue from API', async ({ request }) => {
    // API endpoints are tested by PHPUnit - E2E tests focus on UI interactions
  });

  test.skip('should return notes from API', async ({ request }) => {
    // API endpoints are tested by PHPUnit - E2E tests focus on UI interactions
  });
});
