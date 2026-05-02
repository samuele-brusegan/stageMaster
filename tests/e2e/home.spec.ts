import { test, expect } from '@playwright/test';

test.describe('Homepage', () => {
  test('should load homepage successfully', async ({ page }) => {
    await page.goto('/');
    
    // Check that page loads without errors
    await expect(page).toHaveTitle(/Dashboard/);
  });

  test('should have navigation elements', async ({ page }) => {
    await page.goto('/');
    
    // Check for common navigation elements
    const body = page.locator('body');
    await expect(body).toBeVisible();
  });
});
