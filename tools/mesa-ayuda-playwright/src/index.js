import { chromium } from 'playwright';
import { config, validarConfig } from './config.js';
import { login } from './login.js';
import { getFrames } from './frames.js';
import { abrirDetallePorFolio, extraerRequerimientosPendientes } from './bandeja.js';
import { extraerDetalleRequerimiento } from './detalle.js';
import { extraerYDescargarDocumentos } from './documentos.js';
import { clasificarRequerimiento, resolverRouting } from './clasificador.js';
import { exportarJson } from './exportadorJson.js';
import { log } from './logger.js';

validarConfig();

const browser = await chromium.launch({ headless: config.headless });
const context = await browser.newContext({ acceptDownloads: true });
const page = await context.newPage();

try {
  await login(page, config);
  const { rightFrame } = await getFrames(page);

  const requerimientos = await extraerRequerimientosPendientes(rightFrame);

  for (const req of requerimientos) {
    try {
      await log.info(`Procesando folio ${req.head.folio}`);

      await abrirDetallePorFolio(rightFrame, req.head.folio);
      req.body = await extraerDetalleRequerimiento(rightFrame, req.head.folio);

      if (req.body.tiene_adjuntos) {
        req.body.adjuntos = await extraerYDescargarDocumentos(page, rightFrame, req.head.folio, config.outputDir, config.mesaUrl);
      }

      req.clasificacion = clasificarRequerimiento(req);
      req.routing = resolverRouting(req.clasificacion);
      req.gestion_sugerida = {
        requiere_respuesta: req.clasificacion.requiere_cdp === true,
        tipo_respuesta: req.clasificacion.requiere_cdp === true ? 'certificado_disponibilidad_presupuestaria' : null,
        borrador: null,
        fundamento: null,
        pendientes_de_verificar: [],
        nivel_confianza: req.clasificacion.confianza,
        accion_recomendada: req.clasificacion.requiere_cdp === true ? 'Generar CDP en borrador' : 'Revisión general',
        requiere_confirmacion_usuario: true,
        confirmado_por_usuario: false,
        fecha_confirmacion: null,
      };
      req.capturado_correctamente = true;

      // Vuelve a la bandeja por navegación histórica. Si falla, recarga req_usuario.php en el frame.
      await rightFrame.evaluate(() => {
        if (typeof fvolver === 'function') fvolver();
        else window.location.href = 'req_usuario.php';
      }).catch(async () => {
        await rightFrame.goto(new URL('req_usuario.php', config.mesaUrl).toString());
      });
      await rightFrame.waitForLoadState('domcontentloaded').catch(() => null);
    } catch (error) {
      req.errores.push(error.message);
      req.capturado_correctamente = false;
      await log.error(`Error procesando folio ${req.head?.folio}`, error.message);
    }
  }

  const ruta = await exportarJson(requerimientos, config);
  await log.info(`JSON exportado en ${ruta}`);
} finally {
  await browser.close();
}
