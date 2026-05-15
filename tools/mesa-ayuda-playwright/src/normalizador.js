export function limpiarTexto(value) {
  if (value === null || value === undefined) return null;
  const limpio = String(value)
    .replace(/\u00a0/g, ' ')
    .replace(/[ \t]+/g, ' ')
    .replace(/\n{3,}/g, '\n\n')
    .trim();
  return limpio || null;
}

export function normalizarParaBusqueda(value) {
  return limpiarTexto(value)?.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '') || '';
}

export function safeFilename(name) {
  return limpiarTexto(name)
    ?.replace(/[\\/:*?"<>|]/g, '_')
    .replace(/\s+/g, ' ')
    .trim() || `archivo_${Date.now()}`;
}

export function parseFechaHoraDesdeTexto(texto) {
  const match = String(texto || '').match(/Fecha\s+(.+?)\s+-\s+Hora\s+(.+?)\s+-\s+Estado\s+(.+)/i);
  return {
    fecha: match?.[1]?.trim() || null,
    hora: match?.[2]?.trim() || null,
    estado: match?.[3]?.trim() || null,
  };
}
