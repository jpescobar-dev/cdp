# UI de conexión a Mesa de Ayuda

Este paquete agrega una pantalla visible para ejecutar la conexión/extracción de Mesa de Ayuda desde Laravel.

## Archivos incluidos

- `app/Http/Controllers/MesaAyuda/MesaAyudaExtraccionController.php`
- `resources/views/mesa-ayuda/extracciones/index.blade.php`
- `resources/views/mesa-ayuda/partials/boton-conectar.blade.php`
- `resources/views/layouts/theme/partials/topnavbar.blade.php`
- `routes/mesa_ayuda.php`

## Activar rutas

En `routes/web.php`, agregar:

```php
require __DIR__.'/mesa_ayuda.php';
```

## URL de prueba

Luego abrir:

```text
/mesa-ayuda/extracciones
```

Ahí verás el botón:

```text
Conectar y extraer
```

## Variables mínimas

En `.env`:

```env
MESA_AYUDA_URL=http://mesaayuda.intranet.pjud/mesa_ayuda/index.php
MESA_AYUDA_USER=usuario
MESA_AYUDA_PASSWORD=clave
MESA_AYUDA_HEADLESS=false
MESA_AYUDA_SOLO_LECTURA=true
MESA_AYUDA_PERMITIR_RESPUESTA=false
MESA_AYUDA_PERMITIR_DESCARGA_ADJUNTOS=true
```

## Limpiar caché

```bash
php artisan optimize:clear
php artisan route:clear
php artisan view:clear
```

## Seguridad

La pantalla solo ejecuta extracción/importación. No comenta, no deriva, no soluciona y no anula requerimientos.
