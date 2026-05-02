import { test, expect } from '@playwright/test';

test.describe('Dashboard', () => {
  test('should load dashboard page', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Check that dashboard loads
    await expect(page).toHaveTitle(/Dashboard/);
  });

  test('should have dashboard content', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Check for dashboard elements
    const body = page.locator('body');
    await expect(body).toBeVisible();
  });

  test('should create a screen from the dashboard', async ({ page, request }) => {
    const screenName = `E2E Screen ${Date.now()}`;

    page.on('dialog', async dialog => {
      expect(dialog.type()).toBe('prompt');
      await dialog.accept(screenName);
    });

    const popupPromise = page.waitForEvent('popup');
    await page.goto('/dashboard');
    await page.getByRole('button', { name: 'Crea Schermo' }).click();
    const popup = await popupPromise;
    await popup.waitForLoadState('domcontentloaded');

    await expect(page.getByText(screenName.toUpperCase())).toBeVisible();

    const response = await request.get('/api/screens');
    const result = await response.json();
    const created = result.data.find((screen: { id: number; nome: string }) => screen.nome === screenName);
    expect(created).toBeTruthy();

    if (created) {
      await request.delete(`/api/screens/delete?id=${created.id}`);
    }
  });
});
