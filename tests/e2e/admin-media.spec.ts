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

    // Use existing media from the library instead of registering a fake file
    const mediaResponse = await request.get('/api/media-library');
    const mediaResult = await mediaResponse.json();
    expect(mediaResult.status).toBe('ok');
    // Find an existing image file to use in the test
    const existingImage = mediaResult.data.find((media: { file_type: string; file_name: string }) => 
      media.file_type === 'FOTO' && media.file_name.includes('.jpg')
    );
    expect(existingImage).toBeTruthy();

    await page.goto('/admin');
    const row = page.locator('tr').filter({ hasText: slotName });
    await expect(row.getByRole('cell', { name: slotName })).toBeVisible();
    await row.getByRole('button', { name: 'Media' }).click();

    const wizard = page.locator('#media-wizard-modal');
    await expect(wizard.getByRole('heading', { name: 'Aggiungi media allo slot' })).toBeVisible();
    await wizard.getByRole('button', { name: 'Avanti' }).click();
    // Search for the existing image file
    const imageName = existingImage.file_name.replace(/\.[^/.]+$/, ""); // Remove extension for search
    await wizard.getByPlaceholder('Cerca nella media library...').fill(imageName);
    // Wait for search results to load and be visible
    await wizard.getByRole('button', { name: new RegExp(existingImage.file_name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')) }).waitFor({ state: 'visible', timeout: 10000 });
    await wizard.getByRole('button', { name: new RegExp(existingImage.file_name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')) }).click();
    await wizard.getByRole('button', { name: 'Avanti' }).click();
    await wizard.locator('#wizard-screen-select').selectOption({ index: 0 });
    await wizard.getByRole('button', { name: 'Avanti' }).click();
    await wizard.getByRole('button', { name: 'Salva' }).click();

    await expect(wizard.getByRole('heading', { name: 'Aggiungi media allo slot' })).toBeHidden();

    const queueResponse = await request.get('/api/queue');
    const queueResult = await queueResponse.json();
    expect(queueResult.status).toBe('ok');
    expect(queueResult.data.some((item: { talento_nome: string; file_path: string }) =>
      item.talento_nome === slotName && item.file_path === existingImage.file_path
    )).toBeTruthy();
  });
});
