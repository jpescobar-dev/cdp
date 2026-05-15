import fs from 'node:fs';
import path from 'node:path';

export async function captureFailureEvidence(page, outputDir, label, logger) {
  try {
    const dir = path.join(outputDir, 'errores');
    fs.mkdirSync(dir, { recursive: true });
    const safeLabel = label.replace(/[^a-zA-Z0-9_-]/g, '_');
    const screenshot = path.join(dir, `${safeLabel}.png`);
    const html = path.join(dir, `${safeLabel}.html`);

    await page.screenshot({ path: screenshot, fullPage: true }).catch(() => null);
    const content = await page.content().catch(() => null);
    if (content) fs.writeFileSync(html, content, 'utf8');

    logger?.error('Evidencia de error capturada', { screenshot, html });
    return { screenshot, html };
  } catch (error) {
    logger?.error('No se pudo capturar evidencia del error', { error: error.message });
    return null;
  }
}
