export async function waitForFrame(page, name, timeoutMs = 15000) {
  const start = Date.now();
  while (Date.now() - start < timeoutMs) {
    const frame = page.frame({ name });
    if (frame) return frame;
    await page.waitForTimeout(250);
  }
  throw new Error(`No se encontró el frame ${name}`);
}

export async function getMesaAyudaFrames(page) {
  const rightFrame = await waitForFrame(page, 'right');
  const bandejaEntradaFrame = await waitForFrame(page, 'ifrm1');
  return { rightFrame, bandejaEntradaFrame };
}
