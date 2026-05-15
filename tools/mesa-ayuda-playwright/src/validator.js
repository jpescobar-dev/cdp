export function validarResultadoExtraccion(resultado) {
  const errores = [];

  if (!resultado || typeof resultado !== 'object') errores.push('El resultado no es un objeto.');
  if (!resultado.sistema) errores.push('Falta sistema.');
  if (!resultado.fecha_ejecucion) errores.push('Falta fecha_ejecucion.');
  if (!Array.isArray(resultado.requerimientos)) errores.push('requerimientos debe ser array.');

  const folios = new Set();
  for (const [index, req] of (resultado.requerimientos || []).entries()) {
    const folio = req?.head?.folio;
    if (!folio) errores.push(`requerimientos[${index}].head.folio es obligatorio.`);
    if (folio && folios.has(folio)) errores.push(`Folio duplicado en resultado: ${folio}`);
    if (folio) folios.add(folio);

    if (typeof req.capturado_correctamente !== 'boolean') {
      errores.push(`requerimientos[${index}].capturado_correctamente debe ser boolean.`);
    }

    if (req.capturado_correctamente === false && (!Array.isArray(req.errores) || req.errores.length === 0)) {
      errores.push(`requerimientos[${index}] está marcado con error, pero no registra errores.`);
    }

    const bodyFolio = req?.body?.folio;
    if (folio && bodyFolio && folio !== bodyFolio) {
      errores.push(`requerimientos[${index}] folio head/body no coincide.`);
    }
  }

  return { ok: errores.length === 0, errores };
}

export function marcarRequerimientoIncompleto(req, mensaje) {
  req.capturado_correctamente = false;
  req.errores = Array.isArray(req.errores) ? req.errores : [];
  req.errores.push(mensaje);
  return req;
}
