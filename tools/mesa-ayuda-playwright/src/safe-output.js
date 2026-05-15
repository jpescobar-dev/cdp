import fs from 'node:fs';
import path from 'node:path';
import crypto from 'node:crypto';
import { validarResultadoExtraccion } from './validator.js';

export function ensureDir(dir) {
  fs.mkdirSync(dir, { recursive: true });
}

export function writeJsonSeguro(resultado, outputDir, logger) {
  ensureDir(outputDir);
  const validacion = validarResultadoExtraccion(resultado);
  resultado.validacion_json = validacion;

  const filename = `requerimientos_pendientes_${new Date().toISOString().slice(0, 19).replace(/[:T]/g, '-')}.json`;
  const finalPath = path.join(outputDir, filename);
  const latestPath = path.join(outputDir, 'requerimientos_pendientes.json');
  const tmpPath = `${finalPath}.tmp`;

  const content = JSON.stringify(resultado, null, 2);
  fs.writeFileSync(tmpPath, content, 'utf8');
  fs.renameSync(tmpPath, finalPath);
  fs.copyFileSync(finalPath, latestPath);

  const hash = crypto.createHash('sha256').update(content).digest('hex');
  logger?.info('JSON escrito correctamente', { finalPath, latestPath, hash, validacion });
  return { finalPath, latestPath, hash, validacion };
}
