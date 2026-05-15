import { normalizarParaBusqueda } from './normalizador.js';

export function clasificarRequerimiento(req) {
  const texto = [
    req.head?.componente,
    req.head?.requerimiento,
    req.body?.tipificacion?.texto_original,
    req.body?.tipificacion?.materia,
    req.body?.observacion_principal,
    ...(req.body?.historial || []).map(h => h.observacion),
    ...(req.body?.adjuntos || []).map(a => a.nombre_archivo),
  ].filter(Boolean).join(' ');

  const normalizado = normalizarParaBusqueda(texto);
  let score = 0;
  const evidencias = [];

  const reglas = [
    {
      patron: /certificados? de disponibilidad presupuestaria/,
      puntos: 70,
      fuente: 'tipificacion',
      descripcion: 'Coincide con Certificados de disponibilidad presupuestaria',
    },
    { patron: /\bcdp\b/, puntos: 30, fuente: 'observacion', descripcion: 'Contiene sigla CDP' },
    { patron: /disponibilidad presupuestaria/, puntos: 30, fuente: 'texto', descripcion: 'Contiene disponibilidad presupuestaria' },
    { patron: /solicito\s+cdp|solicitud\s+de\s+cdp/, puntos: 25, fuente: 'observacion', descripcion: 'Solicita CDP expresamente' },
    { patron: /cotizacion|cotizaciones|orden de compra|\boc\b/, puntos: 10, fuente: 'adjuntos', descripcion: 'Menciona antecedentes de compra' },
  ];

  for (const regla of reglas) {
    if (regla.patron.test(normalizado)) {
      score += regla.puntos;
      evidencias.push({ fuente: regla.fuente, descripcion: regla.descripcion });
    }
  }

  if (score >= 70) {
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

export function resolverRouting(clasificacion) {
  if (clasificacion.requiere_cdp === true) {
    return {
      procesar_automaticamente: true,
      destino: 'agente_presupuestario_cdp',
      motivo: 'Clasificado como CDP con confianza alta',
    };
  }

  if (clasificacion.tipo_requerimiento === 'posible_certificado_disponibilidad_presupuestaria') {
    return {
      procesar_automaticamente: false,
      destino: 'revision_usuario',
      motivo: 'Posible CDP; requiere confirmación del usuario',
    };
  }

  return {
    procesar_automaticamente: false,
    destino: 'bandeja_general',
    motivo: 'No corresponde a CDP',
  };
}
