import { test, expect } from '@playwright/test';

test.describe('Projector', () => {
  test('stays black and does not preload assigned screen media before playback', async ({ page }) => {
    let screenShowRequests = 0;

    await page.route('**/api/screens/show?**', route => {
      screenShowRequests += 1;
      return route.fulfill({
        contentType: 'application/json',
        body: JSON.stringify({
          status: 'ok',
          data: {
            media: [{
              id: 1,
              file_path: '/media/static.mp4',
              tipo_media: 'VIDEO',
              file_name: 'static.mp4',
            }],
          },
        }),
      });
    });

    await page.goto('/projector?screen_id=1');
    await page.waitForTimeout(600);

    expect(screenShowRequests).toBe(0);
    await expect(page.locator('#main-video')).toBeHidden();
    await expect(page.locator('#main-image')).toBeHidden();
    await expect(page.locator('#audio-label')).toBeHidden();
    await expect(page.locator('#empty-label')).toBeHidden();
  });

  test('unmutes video after audio activation', async ({ page }) => {
    await page.goto('/projector?screen_id=1');

    await expect(page.locator('#main-video')).toHaveJSProperty('muted', true);
    await expect(page.locator('#main-video')).toHaveJSProperty('defaultMuted', true);

    await page.getByRole('button', { name: 'Attiva audio' }).click();

    await expect(page.locator('#main-video')).toHaveJSProperty('muted', false);
    await expect(page.locator('#main-video')).toHaveJSProperty('defaultMuted', false);
  });
});
