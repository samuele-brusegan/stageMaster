import { test, expect } from '@playwright/test';

test.describe('Admin media wizard', () => {
  test('adds a library media to a slot and queues it', async ({ page, request }) => {
    const slotName = `Wizard Slot ${Date.now()}`;

    const slotResponse = await request.post('/api/talenti/aggiungi', {
      data: {
        nome: slotName,
        categoria: 'E2E',
        materiale_palco: 'Pedana',
        note_luci: 'Bianco frontale',
      },
    });
    const slotResult = await slotResponse.json();
    expect(slotResult.status).toBe('ok');

    const mediaResponse = await request.post('/api/media-library/register', {
      data: {
        files: [
          {
            file_name: 'foto1.jpg',
            file_path: '/media/foto1.jpg',
            file_type: 'FOTO',
            file_size: 0,
          },
        ],
      },
    });
    const mediaResult = await mediaResponse.json();
    expect(mediaResult.status).toBe('ok');

    await page.goto('/admin');
    const row = page.locator('tr').filter({ hasText: slotName });
    await expect(row.getByRole('cell', { name: slotName })).toBeVisible();
    await row.getByRole('button', { name: 'Media' }).click();

    const wizard = page.locator('#media-wizard-modal');
    await expect(wizard.getByRole('heading', { name: 'Aggiungi media allo slot' })).toBeVisible();
    await wizard.getByRole('button', { name: 'Avanti' }).click();
    await wizard.getByPlaceholder('Cerca nella media library...').fill('foto1');
    await wizard.getByRole('button', { name: /foto1\.jpg/ }).click();
    await wizard.getByRole('button', { name: 'Avanti' }).click();
    await wizard.locator('#wizard-screen-select').selectOption({ index: 0 });
    await wizard.getByRole('button', { name: 'Avanti' }).click();
    await wizard.getByRole('button', { name: 'Salva' }).click();

    await expect(wizard.getByRole('heading', { name: 'Aggiungi media allo slot' })).toBeHidden();

    const queueResponse = await request.get('/api/queue');
    const queueResult = await queueResponse.json();
    expect(queueResult.status).toBe('ok');
    expect(queueResult.data.some((item: { talento_nome: string; file_path: string }) =>
      item.talento_nome === slotName && item.file_path === '/media/foto1.jpg'
    )).toBeTruthy();
  });
});
