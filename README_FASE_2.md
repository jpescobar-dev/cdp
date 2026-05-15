# Mesa de Ayuda - Fase 2: integración Laravel + Playwright

Esta fase agrega la integración operacional entre Laravel 10 y el extractor Playwright.

## Contenido

```text
config/mesa_ayuda.php
app/Services/MesaAyuda/PlaywrightExtractorService.php
app/Console/Commands/MesaAyudaExtraerCommand.php
app/Jobs/MesaAyuda/EjecutarExtractorMesaAyudaJob.php
database/seeders/MesaAyudaEstadosSeeder.php
tools/mesa-ayuda-playwright/
```

## Instalación en Laravel

Copia las carpetas sobre la raíz del proyecto Laravel.

Luego agrega al archivo `.env`:

```env
MESA_AYUDA_URL=http://mesaayuda.intranet.pjud/mesa_ayuda/index.php
MESA_AYUDA_USER=usuario
MESA_AYUDA_PASSWORD=clave
MESA_AYUDA_HEADLESS=false
MESA_AYUDA_NODE_BINARY=node
MESA_AYUDA_PLAYWRIGHT_SCRIPT="${APP_BASE_PATH}/tools/mesa-ayuda-playwright/src/index.js"
MESA_AYUDA_PLAYWRIGHT_TIMEOUT=600
MESA_AYUDA_STORAGE_DIR="${APP_BASE_PATH}/storage/app/mesa-ayuda"
```

En Windows/Laragon, si la variable con `${APP_BASE_PATH}` no resuelve bien, deja rutas absolutas o usa los valores por defecto del archivo `config/mesa_ayuda.php`.

## Registrar el comando

En Laravel 10, revisa `app/Console/Kernel.php`.

Agrega el comando si tu proyecto no usa autodiscovery:

```php
protected $commands = [
    \App\Console\Commands\MesaAyudaExtraerCommand::class,
];
```

## Instalar dependencias Playwright

Desde la raíz del proyecto:

```bash
cd tools/mesa-ayuda-playwright
npm install
npx playwright install
```

## Ejecutar seeder de estados

Agrega este seeder a `DatabaseSeeder.php`:

```php
$this->call(\Database\Seeders\MesaAyudaEstadosSeeder::class);
```

Luego ejecuta:

```bash
php artisan db:seed --class=MesaAyudaEstadosSeeder
```

## Ejecutar extractor

Solo extracción:

```bash
php artisan mesa-ayuda:extraer
```

Extracción + importación del JSON a las tablas nuevas:

```bash
php artisan mesa-ayuda:extraer --importar
```

## Resultado esperado

El extractor deja archivos en:

```text
storage/app/mesa-ayuda/extracciones/{id}/requerimientos_pendientes.json
storage/app/mesa-ayuda/extracciones/{id}/adjuntos/{folio}/
storage/app/mesa-ayuda/extracciones/{id}/logs/ejecucion.log
```

## Ojo con la bandeja

El HTML real de la bandeja de entrada todavía no está confirmado. Por eso `tools/mesa-ayuda-playwright/src/bandeja.js` queda con extracción por filas y columnas de tabla, pero puede requerir ajuste fino cuando se copie el `outerHTML` real de la tabla.

## Seguridad

El extractor no presiona botones de gestión como:

- Modificar
- Comentar
- Derivar
- Objetar
- Solucionar
- Anular

Solo lee datos, abre el detalle, abre el popup de documentos y descarga adjuntos.
