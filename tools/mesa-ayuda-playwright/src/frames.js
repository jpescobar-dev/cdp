import { selectors } from './selectors.js';

export async function getFrames(page) {
  await page.waitForLoadState('domcontentloaded');

  for (let i = 0; i < 20; i += 1) {
    const rightFrame = page.frame({ name: selectors.frames.right });
    const leftFrame = page.frame({ name: selectors.frames.left });

    if (rightFrame && leftFrame) {
      return { leftFrame, rightFrame };
    }

    await page.waitForTimeout(500);
  }

  throw new Error('No se encontraron los frames left/right después del login.');
}
