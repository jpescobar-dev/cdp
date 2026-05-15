import { selectors } from './selectors.js';
import { limpiarTexto, parseFechaHoraDesdeTexto } from './normalizador.js';

function parseTipificacion(texto) {
  const partes = String(texto || '')
    .split('->')
    .map(limpiarTexto)
    .filter(Boolean);

  return {
    zona: partes[0] || null,
    area: partes[1] || null,
    materia: partes[2] || partes.at(-1) || null,
    texto_original: limpiarTexto(texto),
  };
}

function extraerEstadoDesdeTitulo(titulo) {
  const match = String(titulo || '').match(/-\s*([^\-]+)$/);
  return limpiarTexto(match?.[1]);
}

async function extraerHistorial(rightFrame) {
  const bloques = await rightFrame.locator(selectors.detalle.bloquesHistorial).all();
  const historial = [];

  for (const bloque of bloques) {
    const texto = limpiarTexto(await bloque.innerText().catch(() => ''));
    if (!texto) continue;

    const lineas = texto.split('\n').map(limpiarTexto).filter(Boolean);
    const encabezado = lineas[0] || '';
    const parsed = parseFechaHoraDesdeTexto(encabezado);

    const accionLinea = lineas.find((x, index) => index > 0 && /:$/.test(x)) || lineas[1] || null;
    const accionIndex = accionLinea ? lineas.indexOf(accionLinea) : -1;
    const usuario = accionIndex >= 0 ? lineas[accionIndex + 1] || null : null;
    const obsIndex = lineas.findIndex(x => /^Observaci[oó]n:?$/i.test(x));

    historial.push({
      fecha: parsed.fecha,
      hora: parsed.hora,
      estado: parsed.estado,
      accion: accionLinea ? accionLinea.replace(/:$/, '') : null,
      usuario_externo: limpiarTexto(usuario),
      observacion: obsIndex >= 0 ? limpiarTexto(lineas.slice(obsIndex + 1).join('\n')) : null,
      texto_original: texto,
    });
  }

  return historial;
}

async function tablaDatosAdicionales(rightFrame) {
  const tablas = await rightFrame.locator('table').all();
  for (const tabla of tablas) {
    const texto = await tabla.innerText().catch(() => '');
    if (texto.includes('Datos adicionales al requerimiento')) return tabla;
  }
  return null;
}

async function extraerDatosAdicionales(rightFrame) {
  const tabla = await tablaDatosAdicionales(rightFrame);
  const texto = tabla ? await tabla.innerText() : '';
  return {
    documento_adjunto: /Ver documentos/i.test(texto),
    tiempo_estimado_solucion: limpiarTexto(texto.match(/Tiempo estimado de soluci[oó]n\s*\n?([^\n]+)/i)?.[1]),
    prioridad: limpiarTexto(texto.match(/Prioridad\s*\n?([^\n]+)/i)?.[1]),
    texto_original: limpiarTexto(texto),
  };
}

async function extraerDatosSolicitante(rightFrame) {
  const tabla = await tablaDatosAdicionales(rightFrame);
  if (!tabla) return {};

  const filas = await tabla.locator('tr').all();
  const data = {};

  for (const fila of filas) {
    const celdas = await fila.locator('td').allInnerTexts().catch(() => []);
    for (let i = 0; i < celdas.length - 1; i += 2) {
      const key = limpiarTexto(celdas[i]);
      const value = limpiarTexto(celdas[i + 1]);
      if (!key || key.includes('Datos Solicitante') || key.includes('Datos adicionales')) continue;
      const normalizedKey = key.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/\s+/g, '_');
      data[normalizedKey] = value;
    }
  }

  return data;
}

export async function extraerDetalleRequerimiento(rightFrame, folioEsperado) {
  await rightFrame.locator(selectors.detalle.form).waitFor({ timeout: 15000 });

  const folio = limpiarTexto(await rightFrame.locator(selectors.detalle.folioHidden).inputValue().catch(() => folioEsperado));
  const titulo = limpiarTexto(await rightFrame.locator(selectors.detalle.tituloRequerimiento).innerText().catch(() => null));
  const tipificacionTexto = limpiarTexto(await rightFrame.locator(selectors.detalle.tipificacion).innerText().catch(() => null));
  const historial = await extraerHistorial(rightFrame);
  const datosAdicionales = await extraerDatosAdicionales(rightFrame);
  const datosSolicitante = await extraerDatosSolicitante(rightFrame);
  const tieneAdjuntos = (await rightFrame.locator(selectors.detalle.linkVerDocumentos).count().catch(() => 0)) > 0;

  return {
    folio,
    estado_actual: extraerEstadoDesdeTitulo(titulo),
    titulo,
    tipificacion: parseTipificacion(tipificacionTexto),
    historial,
    observacion_principal: historial.find(h => h.observacion && /Creado/i.test(h.estado || ''))?.observacion || historial.at(-1)?.observacion || null,
    datos_adicionales: datosAdicionales,
    datos_solicitante: datosSolicitante,
    adjuntos: [],
    tiene_adjuntos: tieneAdjuntos,
  };
}
