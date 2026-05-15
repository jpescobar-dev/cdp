# Fase 9 - Conexión desde Laravel a Mesa de Ayuda

Esta actualización agrega la funcionalidad visible para conectarse a Mesa de Ayuda desde Laravel, ejecutar Playwright, generar JSON, importar y clasificar si los servicios anteriores están instalados.

## Archivos incluidos

- `app/Services/MesaAyuda/EjecutarExtraccionMesaAyudaService.php`
- `app/Http/Controllers/MesaAyuda/MesaAyudaExtraccionController.php`
- `app/Jobs/EjecutarExtraccionMesaAyudaJob.php`
- `app/Console/Commands/ConectarExtraerMesaAyudaCommand.php`
- `config/mesa_ayuda.php`
- `routes/mesa_ayuda.php`
- `resources/views/mesa-ayuda/extracciones/index.blade.php`
- `resources/views/mesa-ayuda/partials/boton-conectar.blade.php`

## Configuración .env mínima

```env
MESA_AYUDA_URL=http://mesaayuda.intranet.pjud/mesa_ayuda/index.php
MESA_AYUDA_USER=usuario
MESA_AYUDA_PASSWORD=clave
MESA_AYUDA_HEADLESS=false
MESA_AYUDA_MAX_FOLIOS=0
MESA_AYUDA_SOLO_LECTURA=true
MESA_AYUDA_PERMITIR_RESPUESTA=false
MESA_AYUDA_PERMITIR_DESCARGA_ADJUNTOS=true
MESA_AYUDA_EXTRACTOR_SCRIPT=tests-playwright/extraer-json-minimo.cjs
MESA_AYUDA_TIMEOUT_PROCESO=900
```

## Ruta

Agregar en `routes/web.php`:

```php
require __DIR__.'/mesa_ayuda.php';
```

Abrir:

```txt
/mesa-ayuda/extracciones
```

## Comando de consola alternativo

```bash
php artisan mesa-ayuda:conectar-extraer --max-folios=1
php artisan mesa-ayuda:conectar-extraer --max-folios=0
```

## Seguridad

La ejecución valida que:

- `MESA_AYUDA_SOLO_LECTURA=true`
- `MESA_AYUDA_PERMITIR_RESPUESTA=false`

Esta fase no comenta, no deriva, no soluciona, no anula y no responde requerimientos.

## Después de copiar

```bash
php artisan optimize:clear
php artisan route:clear
php artisan view:clear
php artisan config:clear
```
