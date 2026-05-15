import 'dotenv/config';
import path from 'node:path';

export const config = {
  mesaUrl: process.env.MESA_URL || 'http://mesaayuda.intranet.pjud/mesa_ayuda/index.php',
  user: process.env.MESA_USER,
  password: process.env.MESA_PASSWORD,
  headless: String(process.env.HEADLESS || 'false').toLowerCase() === 'true',
  outputDir: path.resolve(process.env.OUTPUT_DIR || 'output'),
  extractionId: process.env.EXTRACTION_ID || 'local',
};

export function validarConfig() {
  const faltantes = [];
  if (!config.user) faltantes.push('MESA_USER');
  if (!config.password) faltantes.push('MESA_PASSWORD');
  if (faltantes.length) {
    throw new Error(`Faltan variables de entorno: ${faltantes.join(', ')}`);
  }
}
