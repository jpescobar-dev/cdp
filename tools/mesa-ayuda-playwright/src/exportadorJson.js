import fs from 'node:fs/promises';
import path from 'node:path';

export async function exportarJson(requerimientos, config) {
  await fs.mkdir(config.outputDir, { recursive: true });

  const data = {
    sistema: 'Mesa de Ayuda - Sistema de Requerimientos',
    url_origen: config.mesaUrl,
    extraction_id: config.extractionId,
    fecha_ejecucion: new Date().toISOString(),
    total_requerimientos_pendientes: requerimientos.length,
    requerimientos,
  };

  const ruta = path.join(config.outputDir, 'requerimientos_pendientes.json');
  await fs.writeFile(ruta, JSON.stringify(data, null, 2), 'utf8');
  return ruta;
}
