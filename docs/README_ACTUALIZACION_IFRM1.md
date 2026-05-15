# Actualización extractor Mesa de Ayuda - frame `ifrm1`

## Qué corrige

Esta actualización corrige la lectura de la bandeja de entrada de Mesa de Ayuda.

Se confirmó en prueba real que:

- `right` contiene encabezados, título de bandeja y los iframes internos.
- `ifrm1` contiene las filas reales de requerimientos pendientes.
- `ifrm2` contiene la bandeja de salida.

Por lo tanto, el extractor debe leer los requerimientos pendientes desde el frame:

```text
ifrm1
```

No desde `right`.

## Archivos incluidos

```text
tests-playwright/extraer-json-minimo.cjs
tools/mesa-ayuda-playwright/src/bandeja.js
tools/mesa-ayuda-playwright/src/frame-utils.js
```

## Instalación

Copiar los archivos en la raíz del proyecto Laravel.

Si Windows pregunta si desea reemplazar archivos existentes, aceptar.

## Prueba directa

Desde la raíz del proyecto:

```bat
set MESA_AYUDA_USER=tu_usuario
set MESA_AYUDA_PASSWORD=tu_clave
set MESA_AYUDA_URL=http://mesaayuda.intranet.pjud/mesa_ayuda/index.php
set MESA_AYUDA_HEADLESS=false
node tests-playwright\extraer-json-minimo.cjs
```

También puede ejecutarse desde `tests-playwright`:

```bat
set MESA_AYUDA_USER=tu_usuario
set MESA_AYUDA_PASSWORD=tu_clave
node extraer-json-minimo.cjs
```

## Resultado esperado

La consola debe mostrar algo similar a:

```text
Frame right encontrado: http://mesaayuda.intranet.pjud/mesa_ayuda/req_usuario.php
Frame bandeja entrada encontrado: http://mesaayuda.intranet.pjud/mesa_ayuda/req_usuarioi1.php?rut=...
Requerimientos detectados: 3
[ '7954868', '7953726', '7950738' ]
Procesando folio 7954868...
Procesando folio 7953726...
Procesando folio 7950738...
JSON generado en: C:\laragon\www\cdp\storage\app\mesa-ayuda\pruebas\requerimientos_prueba_....json
```

## Adjuntos

Por defecto, esta prueba solo lista adjuntos. Para intentar descargarlos:

```bat
set MESA_AYUDA_DESCARGAR_ADJUNTOS=true
node tests-playwright\extraer-json-minimo.cjs
```

Los adjuntos se guardarán en:

```text
storage/app/mesa-ayuda/adjuntos/{folio}/
```

## Seguridad

Esta actualización no presiona botones de gestión como:

- Comentar
- Derivar
- Objetar
- Solucionar
- Anular
- Modificar

Solo realiza lectura, navegación al detalle y lectura/listado opcional de documentos adjuntos.
