# Corrección topnavbar - Mesa de Ayuda

Reemplazar el archivo:

`resources/views/layouts/theme/partials/topnavbar.blade.php`

por el incluido en este ZIP.

Cambios relevantes:

- Menú visible de Mesa de Ayuda.
- Menú visible de Agentes.
- Se usa `data-toggle="collapse"`, compatible con Bootstrap 4.
- Se evita que el menú rompa si una ruta aún no existe, usando `Route::has()`.
- Se corrigió el enlace de Presupuesto > Expedientes.

Después de copiar:

```bash
php artisan optimize:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear
```

Luego recarga el navegador con Ctrl + F5.
