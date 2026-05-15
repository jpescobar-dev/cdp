# Fase 7 — Importación del JSON de Mesa de Ayuda a Laravel

Esta fase toma el JSON generado por Playwright y lo guarda en las tablas internas:

- `mesa_ayuda_extracciones`
- `mesa_ayuda_requerimientos`
- `mesa_ayuda_historial`
- `mesa_ayuda_adjuntos`

## Archivos incluidos

```text
app/Services/MesaAyuda/ImportarJsonMesaAyudaService.php
app/Console/Commands/ImportarJsonMesaAyudaCommand.php
```

## Instalación

Copiar los archivos respetando las rutas dentro del proyecto Laravel.

Luego ejecutar:

```bash
php artisan optimize:clear
composer dump-autoload
```

## Uso recomendado

Importar el último JSON generado por Playwright:

```bash
php artisan mesa-ayuda:importar-json --latest
```

Importar un archivo específico:

```bash
php artisan mesa-ayuda:importar-json storage/app/mesa-ayuda/pruebas/requerimientos_prueba_1778031284845.json
```

Si quieres registrar el RUT del usuario que ejecuta la importación:

```bash
php artisan mesa-ayuda:importar-json --latest --usuario=10834961
```

## Qué hace el importador

1. Crea una ejecución en `mesa_ayuda_extracciones`.
2. Recorre cada requerimiento del JSON.
3. Crea o actualiza `mesa_ayuda_requerimientos` por folio.
4. Reemplaza el historial externo asociado al folio.
5. Crea o actualiza los adjuntos asociados al folio.
6. Guarda `head_json`, `body_json` y `json_completo` para trazabilidad.
7. Evita duplicar requerimientos si vuelves a importar el mismo folio.

## Consideración importante

Este comando no genera CDP, no crea expedientes presupuestarios y no responde Mesa de Ayuda.

Su alcance es solo:

```text
JSON Playwright → Base de datos Laravel
```

Después de validar esta fase, el siguiente paso es clasificar formalmente CDP/no CDP desde Laravel.
