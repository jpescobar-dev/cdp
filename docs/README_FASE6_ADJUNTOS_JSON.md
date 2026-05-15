# Fase 6 - Extracción completa base y adjuntos

Esta actualización consolida la extracción real desde Mesa de Ayuda:

- Lee la bandeja de entrada desde el iframe interno `ifrm1`.
- Recorre todos los folios pendientes visibles.
- Abre cada detalle en el frame `right`.
- Extrae historial, tipificación, datos adicionales y datos del solicitante.
- Abre el popup `Ver documentos`.
- Lista y descarga los documentos desde `ver_documentos_detalle.php`.
- Guarda adjuntos en `storage/app/mesa-ayuda/adjuntos/{folio}/`.
- Genera JSON en `storage/app/mesa-ayuda/pruebas/`.
- Clasifica preliminarmente si corresponde a CDP.

## Prueba

Desde la raíz del proyecto:

```bat
set MESA_AYUDA_USER=tu_usuario
set MESA_AYUDA_PASSWORD=tu_clave
set MESA_AYUDA_URL=http://mesaayuda.intranet.pjud/mesa_ayuda/index.php
set MESA_AYUDA_HEADLESS=false
set MESA_AYUDA_PERMITIR_DESCARGA_ADJUNTOS=true
node tests-playwright\extraer-json-minimo.cjs
```

Para limitar la prueba a un solo folio:

```bat
set MESA_AYUDA_MAX_FOLIOS=1
node tests-playwright\extraer-json-minimo.cjs
```

## Variables relevantes

- `MESA_AYUDA_USER`
- `MESA_AYUDA_PASSWORD`
- `MESA_AYUDA_URL`
- `MESA_AYUDA_HEADLESS`
- `MESA_AYUDA_PERMITIR_DESCARGA_ADJUNTOS`
- `MESA_AYUDA_MAX_FOLIOS`

## Criterio de éxito

La consola debe mostrar:

```text
Requerimientos detectados: N
Procesando folio ...
JSON generado en: ...
Resumen: { total: N, errores: 0, capturados: N }
```

Y deben existir archivos descargados en:

```text
storage/app/mesa-ayuda/adjuntos/{folio}/
```
