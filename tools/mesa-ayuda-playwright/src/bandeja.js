function normalizarTexto(texto) {
  return (texto || '').replace(/\u00a0/g, ' ').replace(/[ \t]+/g, ' ').trim();
}

export async function extraerHeadDesdeBandeja(bandejaFrame, maxFolios = 0) {
  await bandejaFrame.locator('table tr').first().waitFor({ timeout: 15000 });
  const filas = await bandejaFrame.locator('table tr').all();
  const requerimientos = [];

  for (const fila of filas) {
    const celdas = await fila.locator('td').allInnerTexts();
    const celdasLimpias = celdas.map(normalizarTexto);
    if (celdasLimpias.length < 10) continue;

    const linkFolio = fila.locator('td').nth(1).locator('a').first();
    if (!(await linkFolio.count())) continue;

    const folio = normalizarTexto(await linkFolio.innerText());
    const href = await linkFolio.getAttribute('href').catch(() => null);
    if (!/^\d{6,}$/.test(folio)) continue;

    requerimientos.push({
      head: {
        folio,
        fecha_hora: celdasLimpias[2] || null,
        estado: celdasLimpias[3] || null,
        componente: celdasLimpias[4] || null,
        requerimiento: celdasLimpias[5] || null,
        tribunal: celdasLimpias[6] || null,
        solicitado_por: celdasLimpias[7] || null,
        solicitado_para: celdasLimpias[8] || null,
        tiempo_estimado_solucion: celdasLimpias[9] || null,
        celdas_raw: celdasLimpias,
      },
      href,
      body: null,
      capturado_correctamente: false,
      errores: [],
    });
  }

  return maxFolios > 0 ? requerimientos.slice(0, maxFolios) : requerimientos;
}
