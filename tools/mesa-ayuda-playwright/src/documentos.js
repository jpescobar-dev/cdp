import fs from 'fs';
import path from 'path';
import crypto from 'crypto';

function normalizarTexto(texto) {
  return (texto || '').replace(/\u00a0/g, ' ').replace(/[ \t]+/g, ' ').trim();
}

function sanitizeFileName(nombre) {
  return normalizarTexto(nombre)
    .replace(/[<>:"/\\|?*\x00-\x1F]/g, '_')
    .replace(/\s+/g, ' ')
    .slice(0, 180) || `archivo_${Date.now()}`;
}

function sha256(buffer) {
  return crypto.createHash('sha256').update(buffer).digest('hex');
}

export async function extraerYDescargarDocumentos({ page, context, rightFrame, folio, projectRoot, adjuntosBase, permitirDescarga = true }) {
  const existeLink = await rightFrame.locator('a:has-text("Ver documentos")').count();
  if (!existeLink) return [];

  const [popup] = await Promise.all([
    page.waitForEvent('popup'),
    rightFrame.locator('a:has-text("Ver documentos")').click(),
  ]);

  await popup.waitForLoadState('domcontentloaded');

  const links = await popup.locator('a[href*="ver_documentos_detalle.php"]').all();
  const documentos = [];
  const folioDir = path.join(adjuntosBase, String(folio));
  fs.mkdirSync(folioDir, { recursive: true });

  for (const link of links) {
    const nombreOriginal = normalizarTexto(await link.innerText());
    const nombreArchivo = sanitizeFileName(nombreOriginal);
    const href = await link.getAttribute('href');
    const urlAbsoluta = new URL(href, popup.url()).toString();
    const rutaLocal = path.join(folioDir, nombreArchivo);

    const item = {
      nombre_archivo: nombreArchivo,
      nombre_original: nombreOriginal,
      href,
      url_absoluta: urlAbsoluta,
      descargado: false,
      ruta_local: path.relative(projectRoot, rutaLocal).replace(/\\/g, '/'),
      tipo_mime: null,
      tamano_bytes: null,
      hash_archivo: null,
      error: null,
    };

    if (!permitirDescarga) {
      item.error = 'Descarga omitida por configuración';
      documentos.push(item);
      continue;
    }

    try {
      const response = await context.request.get(urlAbsoluta, { timeout: 60000 });
      if (!response.ok()) throw new Error(`HTTP ${response.status()} al descargar ${nombreArchivo}`);
      const buffer = await response.body();
      fs.writeFileSync(rutaLocal, buffer);
      item.descargado = true;
      item.tipo_mime = response.headers()['content-type'] || null;
      item.tamano_bytes = buffer.length;
      item.hash_archivo = sha256(buffer);
    } catch (error) {
      item.error = error.message;
    }

    documentos.push(item);
  }

  await popup.close().catch(() => {});
  return documentos;
}
