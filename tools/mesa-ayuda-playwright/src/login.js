import { selectors } from './selectors.js';
import { log } from './logger.js';

export async function login(page, config) {
  await log.info('Abriendo Mesa de Ayuda');
  await page.goto(config.mesaUrl, { waitUntil: 'domcontentloaded' });

  const formVisible = await page.locator(selectors.login.form).isVisible().catch(() => false);
  if (!formVisible) {
    await log.warn('No se detectó formulario de login. Puede existir sesión activa.');
    return;
  }

  await page.locator(selectors.login.username).fill(config.user);
  await page.locator(selectors.login.password).fill(config.password);

  await Promise.all([
    page.waitForLoadState('domcontentloaded'),
    page.locator(selectors.login.submit).click(),
  ]);

  await log.info('Login enviado');
}
