export const selectors = {
  login: {
    form: 'form[name="frm"]',
    username: 'input[name="username"]',
    password: 'input[name="password"]',
    submit: 'button[type="submit"]',
  },
  frames: {
    left: 'left',
    right: 'right',
  },
  bandeja: {
    titulo: 'text=Bandeja de entrada',
    tablas: 'table',
    filas: 'tr',
    celdas: 'td',
    links: 'a',
    // Pendiente de ajustar con HTML real de bandeja.
    linkFolio: 'a[href*="detallerequerimiento"], a',
  },
  detalle: {
    form: 'form[name="frm"]',
    folioHidden: 'input[name="crr_requerimiento_id"]',
    tituloRequerimiento: 'tr.tdtitulo_req td',
    tipificacion: 'tr.tdtitulo111 td:nth-child(2)',
    bloquesHistorial: 'table[bordercolor="#336699"]',
    linkVerDocumentos: 'a:has-text("Ver documentos")',
  },
  documentos: {
    links: 'a[href*="ver_documentos_detalle.php"]',
  },
};
