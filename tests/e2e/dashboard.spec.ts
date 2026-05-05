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
    // The popup handles screen creation, then we check the main page
    // Wait for the screen to appear on the main dashboard
    await expect(page.getByText(screenName.toUpperCase(), { exact: true })).toBeVisible({ timeout: 10000 });

    const response = await request.get('/api/screens');
    const result = await response.json();
    const created = result.data.find((screen: { id: number; nome: string }) => screen.nome === screenName);
    expect(created).toBeTruthy();

    if (created) {
      await request.delete(`/api/screens/delete?id=${created.id}`);
    }
  });

  test('keeps the timeline on the selected slot when the first slot responds late', async ({ page }) => {
    let resolveFirstTimeline: (() => void) | null = null;
    const firstTimelineCanRespond = new Promise<void>(resolve => {
      resolveFirstTimeline = resolve;
    });

    await page.route('**/api/talenti', route => route.fulfill({
      contentType: 'application/json',
      body: JSON.stringify({
        status: 'ok',
        data: [
          { id: 1, nome: 'Slot Uno' },
          { id: 2, nome: 'Slot Due' },
        ],
      }),
    }));

    await page.route('**/api/queue', route => route.fulfill({
      contentType: 'application/json',
      body: JSON.stringify({ status: 'ok', data: [] }),
    }));

    await page.route('**/api/screens', route => route.fulfill({
      contentType: 'application/json',
      body: JSON.stringify({ status: 'ok', data: [] }),
    }));

    await page.route('**/api/notes/grouped?**', route => route.fulfill({
      contentType: 'application/json',
      body: JSON.stringify({
        status: 'ok',
        data: { materiale_palco: [], luci: [] },
      }),
    }));

    await page.route('**/api/media/talento?**', async route => {
      const url = new URL(route.request().url());
      const slotId = url.searchParams.get('talento_id');

      if (slotId === '1') {
        await firstTimelineCanRespond;
        await route.fulfill({
          contentType: 'application/json',
          body: JSON.stringify([
            {
              id: 101,
              friendly_name: 'Clip Primo Slot',
              file_path: '/media/first.mp4',
              tipo_media: 'VIDEO',
              screen_id: 1,
              screen_nome: 'Screen 1',
              timestamp_inizio: '00:00:00',
              durata_totale_sec: 10,
            },
          ]),
        });
        return;
      }

      await route.fulfill({
        contentType: 'application/json',
        body: JSON.stringify([
          {
            id: 202,
            friendly_name: 'Clip Secondo Slot',
            file_path: '/media/second.mp4',
            tipo_media: 'VIDEO',
            screen_id: 1,
            screen_nome: 'Screen 1',
            timestamp_inizio: '00:00:00',
            durata_totale_sec: 10,
          },
        ]),
      });
    });

    await page.goto('/dashboard');
    await page.getByText('Slot Due', { exact: true }).click();

    await expect(page.locator('#current-slot-name')).toHaveText('- Slot Due');
    await expect(page.getByText('Clip Secondo Slot')).toBeVisible();

    resolveFirstTimeline?.();

    await expect(page.getByText('Clip Secondo Slot')).toBeVisible();
    await expect(page.getByText('Clip Primo Slot')).toHaveCount(0);
  });

  test('does not preload static screen media before playback starts', async ({ page }) => {
    let screenShowRequests = 0;

    await page.route('**/api/talenti', route => route.fulfill({
      contentType: 'application/json',
      body: JSON.stringify({ status: 'ok', data: [] }),
    }));

    await page.route('**/api/queue', route => route.fulfill({
      contentType: 'application/json',
      body: JSON.stringify({ status: 'ok', data: [] }),
    }));

    await page.route('**/api/screens', route => route.fulfill({
      contentType: 'application/json',
      body: JSON.stringify({
        status: 'ok',
        data: [{ id: 1, nome: 'Main', tipo: 'indipendente' }],
      }),
    }));

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

    await page.goto('/dashboard');
    await expect(page.getByText('MAIN', { exact: true })).toBeVisible();
    await page.waitForTimeout(500);

    expect(screenShowRequests).toBe(0);
    await expect(page.locator('#screen-1-video')).toBeHidden();
    await expect(page.locator('#screen-1-image')).toBeHidden();
    await expect(page.locator('#screen-1-placeholder')).toBeHidden();
  });
});
