const fs = require('fs');
const path = require('path');
const crypto = require('crypto');
const { chromium } = require('playwright');

const MESA_URL = process.env.MESA_AYUDA_URL || 'http://mesaayuda.intranet.pjud/mesa_ayuda/index.php';
const USER = process.env.MESA_AYUDA_USER;
const PASSWORD = process.env.MESA_AYUDA_PASSWORD;
const HEADLESS = String(process.env.MESA_AYUDA_HEADLESS || 'false').toLowerCase() === 'true';
const PERMITIR_DESCARGA = String(process.env.MESA_AYUDA_PERMITIR_DESCARGA_ADJUNTOS || 'true').toLowerCase() !== 'false';
const MAX_FOLIOS = Number(process.env.MESA_AYUDA_MAX_FOLIOS || 0); // 0 = todos

if (!USER || !PASSWORD) {
  console.error('Faltan variables MESA_AYUDA_USER o MESA_AYUDA_PASSWORD');
  process.exit(1);
}

const projectRoot = path.basename(process.cwd()).toLowerCase() === 'tests-playwright'
  ? path.dirname(process.cwd())
  : process.cwd();

const outputBase = path.join(projectRoot, 'storage', 'app', 'mesa-ayuda', 'pruebas');
const adjuntosBase = path.join(projectRoot, 'storage', 'app', 'mesa-ayuda', 'adjuntos');

function normalizarTexto(texto) {
  return (texto || '')
    .replace(/\u00a0/g, ' ')
    .replace(/[ \t]+/g, ' ')
    .replace(/\n\s+\n/g, '\n')
    .trim();
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

function parsearTipificacion(texto) {
  const partes = normalizarTexto(texto)
    .split('->')
    .map(normalizarTexto)
    .filter(Boolean);

  return {
    zona: partes[0] || null,
    area: partes[1] || null,
    materia: partes[2] || null,
    texto_original: normalizarTexto(texto),
  };
}

async function waitForFrame(page, name, timeoutMs = 15000) {
  const start = Date.now();
  while (Date.now() - start < timeoutMs) {
    const frame = page.frame({ name });
    if (frame) return frame;
    await page.waitForTimeout(250);
  }
  throw new Error(`No se encontró el frame ${name}`);
}

async function login(page) {
  await page.goto(MESA_URL, { waitUntil: 'domcontentloaded', timeout: 60000 });
  await page.locator('input[name="username"]').fill(USER);
  await page.locator('input[name="password"]').fill(PASSWORD);

  await Promise.all([
    page.waitForLoadState('domcontentloaded'),
    page.locator('button[type="submit"]').click(),
  ]);

  await page.waitForTimeout(3000);
}

async function extraerHeadDesdeBandeja(bandejaFrame) {
  await bandejaFrame.locator('table tr').first().waitFor({ timeout: 15000 });

  const filas = await bandejaFrame.locator('table tr').all();
  const requerimientos = [];

  for (const fila of filas) {
    const celdas = await fila.locator('td').allInnerTexts();
    const celdasLimpias = celdas.map(normalizarTexto);

    // Estructura real ifrm1:
    // [0 prioridad/adjunto], [1 folio], [2 fecha], [3 estado], [4 componente], [5 requerimiento],
    // [6 tribunal], [7 solicitado por], [8 solicitado para], [9 tiempo estimado]
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

  return MAX_FOLIOS > 0 ? requerimientos.slice(0, MAX_FOLIOS) : requerimientos;
}

async function extraerHistorial(rightFrame) {
  const bloques = await rightFrame.locator('table[bordercolor="#336699"]').all();
  const historial = [];

  for (const bloque of bloques) {
    const texto = normalizarTexto(await bloque.innerText().catch(() => ''));
    const lineas = texto.split('\n').map(normalizarTexto).filter(Boolean);
    const encabezado = lineas[0] || '';
    const match = encabezado.match(/Fecha\s+(.+?)\s+-\s+Hora\s+(.+?)\s+-\s+Estado\s+(.+)/i);

    let accion = null;
    let usuario = null;
    let observacion = null;

    for (let i = 1; i < lineas.length; i++) {
      const linea = lineas[i];
      if (/Derivado a:|Creado por:|Asignado a:|Modificado por:/i.test(linea)) {
        accion = linea.replace(':', '').trim();
        usuario = lineas[i + 1] || null;
      }
      if (/Observación:/i.test(linea)) {
        observacion = lineas.slice(i + 1).join('\n') || null;
      }
    }

    historial.push({
      fecha: match?.[1]?.trim() || null,
      hora: match?.[2]?.trim() || null,
      estado: match?.[3]?.trim() || null,
      accion,
      usuario,
      observacion,
      texto_original: texto,
    });
  }

  return historial;
}

async function extraerDatosAdicionalesYSolicitante(rightFrame) {
  const texto = normalizarTexto(await rightFrame.locator('body').innerText().catch(() => ''));
  const lineas = texto.split('\n').map(normalizarTexto).filter(Boolean);

  const buscarValorPosterior = (etiqueta) => {
    const idx = lineas.findIndex(l => l.toLowerCase().replace(':', '') === etiqueta.toLowerCase().replace(':', ''));
    if (idx >= 0) return lineas[idx + 1] || null;
    return null;
  };

  return {
    datos_adicionales: {
      documento_adjunto: await rightFrame.locator('a:has-text("Ver documentos")').count().then(c => c > 0).catch(() => false),
      tiempo_estimado_solucion: texto.match(/Tiempo estimado de solución\s+([^\n]+)/i)?.[1]?.trim() || null,
      prioridad: texto.match(/Prioridad\s+([^\n]+)/i)?.[1]?.trim() || null,
    },
    datos_solicitante: {
      nombre: buscarValorPosterior('Nombre'),
      rut: buscarValorPosterior('Rut'),
      direccion: buscarValorPosterior('Dirección'),
      fono_contacto: buscarValorPosterior('Fono Contacto'),
      tribunal: buscarValorPosterior('Tribunal'),
      cargo: buscarValorPosterior('Cargo'),
    }
  };
}

async function extraerDetalle(rightFrame) {
  await rightFrame.locator('form[name="frm"]').waitFor({ timeout: 15000 });

  const folio = await rightFrame.locator('input[name="crr_requerimiento_id"]').inputValue().catch(() => null);
  const titulo = await rightFrame.locator('tr.tdtitulo_req td').innerText().catch(() => null);
  const tipificacionTexto = await rightFrame.locator('tr.tdtitulo111 td').nth(1).innerText().catch(() => null);
  const historial = await extraerHistorial(rightFrame);
  const datos = await extraerDatosAdicionalesYSolicitante(rightFrame);

  const tieneAdjuntos = await rightFrame.locator('a:has-text("Ver documentos")').count().then(count => count > 0).catch(() => false);

  return {
    folio,
    titulo: normalizarTexto(titulo),
    estado_actual: normalizarTexto(titulo)?.split('-')?.pop()?.trim() || null,
    tipificacion: parsearTipificacion(tipificacionTexto),
    historial,
    datos_adicionales: datos.datos_adicionales,
    datos_solicitante: datos.datos_solicitante,
    tiene_adjuntos: tieneAdjuntos,
    adjuntos: [],
  };
}

async function extraerYDescargarDocumentos(page, context, rightFrame, folio) {
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

    if (!PERMITIR_DESCARGA) {
      item.error = 'Descarga omitida por MESA_AYUDA_PERMITIR_DESCARGA_ADJUNTOS=false';
      documentos.push(item);
      continue;
    }

    try {
      const response = await context.request.get(urlAbsoluta, { timeout: 60000 });
      if (!response.ok()) {
        throw new Error(`HTTP ${response.status()} al descargar ${nombreArchivo}`);
      }

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

function clasificar(req) {
  const texto = [
    req.head?.componente,
    req.head?.requerimiento,
    req.body?.tipificacion?.texto_original,
    ...(req.body?.historial || []).map(h => h.observacion || h.texto_original),
    ...(req.body?.adjuntos || []).map(a => a.nombre_archivo),
  ].filter(Boolean).join(' ').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');

  let score = 0;
  const evidencias = [];

  const reglas = [
    [/certificados? de disponibilidad presupuestaria/i, 60, 'Tipificación o texto contiene Certificados de disponibilidad presupuestaria'],
    [/\bcdp\b/i, 30, 'Texto contiene sigla CDP'],
    [/disponibilidad presupuestaria/i, 30, 'Texto contiene disponibilidad presupuestaria'],
    [/solicito.*cdp|solicitud.*cdp/i, 25, 'Observación solicita CDP'],
    [/cotizacion|cotizaciones|orden de compra|\boc\b|presupuesto/i, 10, 'Adjuntos o texto asociados a compra/cotización'],
  ];

  for (const [patron, puntos, evidencia] of reglas) {
    if (patron.test(texto)) {
      score += puntos;
      evidencias.push(evidencia);
    }
  }

  if (score >= 60) {
    return {
      tipo_requerimiento: 'certificado_disponibilidad_presupuestaria',
      requiere_cdp: true,
      confianza: 'alta',
      score,
      evidencias,
      requiere_revision_usuario: false,
    };
  }

  if (score >= 35) {
    return {
      tipo_requerimiento: 'posible_certificado_disponibilidad_presupuestaria',
      requiere_cdp: null,
      confianza: 'media',
      score,
      evidencias,
      requiere_revision_usuario: true,
    };
  }

  return {
    tipo_requerimiento: 'otro',
    requiere_cdp: false,
    confianza: 'baja',
    score,
    evidencias,
    requiere_revision_usuario: false,
  };
}

(async () => {
  const browser = await chromium.launch({ headless: HEADLESS });
  const context = await browser.newContext({ acceptDownloads: true });
  const page = await context.newPage();

  const resultado = {
    sistema: 'Mesa de Ayuda - Sistema de Requerimientos',
    url_origen: MESA_URL,
    fecha_ejecucion: new Date().toISOString(),
    total_requerimientos_pendientes: 0,
    resumen: { total: 0, errores: 0, capturados: 0 },
    requerimientos: [],
    errores: [],
  };

  try {
    await login(page);

    const rightFrame = await waitForFrame(page, 'right');
    console.log('Frame right encontrado:', rightFrame.url());

    let bandejaFrame = await waitForFrame(page, 'ifrm1');
    console.log('Frame bandeja entrada encontrado:', bandejaFrame.url());

    const requerimientos = await extraerHeadDesdeBandeja(bandejaFrame);
    console.log(`Requerimientos detectados: ${requerimientos.length}`);
    console.log(requerimientos.map(r => r.head.folio));

    for (const req of requerimientos) {
      try {
        console.log(`Procesando folio ${req.head.folio}...`);

        bandejaFrame = await waitForFrame(page, 'ifrm1');

        await bandejaFrame
          .locator('a')
          .filter({ hasText: req.head.folio })
          .first()
          .click();

        await rightFrame.locator('form[name="frm"]').waitFor({ timeout: 15000 });

        const body = await extraerDetalle(rightFrame);
        body.adjuntos = await extraerYDescargarDocumentos(page, context, rightFrame, body.folio || req.head.folio);

        req.body = body;
        req.clasificacion = clasificar(req);
        req.routing = req.clasificacion.requiere_cdp === true
          ? { procesar_automaticamente: true, destino: 'agente_presupuestario_cdp', motivo: 'Clasificado como CDP con confianza alta' }
          : { procesar_automaticamente: false, destino: req.clasificacion.confianza === 'media' ? 'revision_usuario' : 'bandeja_general', motivo: 'No clasificado como CDP con confianza alta' };
        req.capturado_correctamente = true;

        resultado.requerimientos.push(req);

        await rightFrame.goto(new URL('req_usuario.php', MESA_URL).toString(), {
          waitUntil: 'domcontentloaded',
          timeout: 30000,
        });
        await waitForFrame(page, 'ifrm1');
        await page.waitForTimeout(800);
      } catch (errorReq) {
        req.errores.push({ mensaje: errorReq.message, stack: errorReq.stack });
        req.capturado_correctamente = false;
        resultado.requerimientos.push(req);
        console.error(`Error procesando folio ${req.head.folio}:`, errorReq.message);

        await rightFrame.goto(new URL('req_usuario.php', MESA_URL).toString(), {
          waitUntil: 'domcontentloaded',
          timeout: 30000,
        }).catch(() => {});
        await page.waitForTimeout(800);
      }
    }

    resultado.total_requerimientos_pendientes = resultado.requerimientos.length;
    resultado.resumen.total = resultado.requerimientos.length;
    resultado.resumen.errores = resultado.requerimientos.filter(r => !r.capturado_correctamente).length;
    resultado.resumen.capturados = resultado.requerimientos.filter(r => r.capturado_correctamente).length;
  } catch (error) {
    resultado.errores.push({ mensaje: error.message, stack: error.stack });
    console.error('Error general:', error);
  }

  fs.mkdirSync(outputBase, { recursive: true });
  const outputPath = path.join(outputBase, `requerimientos_prueba_${Date.now()}.json`);
  fs.writeFileSync(outputPath, JSON.stringify(resultado, null, 2), 'utf8');

  console.log('JSON generado en:', outputPath);
  console.log('Resumen:', resultado.resumen);

  await browser.close();
})();
