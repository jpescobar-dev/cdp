# Módulo Mesa de Ayuda / CDP - Archivos base Laravel 10

Este ZIP contiene una primera capa de arquitectura para integrar la captura de requerimientos desde Mesa de Ayuda en una aplicación Laravel 10 existente.

## Contenido

### Migraciones nuevas

- `mesa_ayuda_extracciones`
- `mesa_ayuda_requerimientos`
- `mesa_ayuda_historial`
- `mesa_ayuda_adjuntos`
- `cdp_borradores`
- `agente_interacciones`
- `mesa_ayuda_respuestas`

### Modelos nuevos

- `MesaAyudaExtraccion`
- `MesaAyudaRequerimiento`
- `MesaAyudaHistorial`
- `MesaAyudaAdjunto`
- `ExpedientePresupuestario`
- `ExpedienteHistorial`
- `ExpedienteObservacion`
- `ExpedienteTarea`
- `ExpedienteAdjunto`
- `CdpBorrador`
- `AgenteInteraccion`
- `MesaAyudaRespuesta`

### Servicios incluidos

- `App\Services\MesaAyuda\ClasificadorRequerimientoService`
- `App\Services\MesaAyuda\ImportarRequerimientosMesaAyudaService`

## Instalación

1. Copiar las carpetas `app` y `database` sobre la raíz del proyecto Laravel.
2. Revisar que existan estos modelos/tablas en tu proyecto:
   - `Funcionario` / `funcionarios`
   - `Estado` / `estados`
   - `Ccosto` / `ccostos`
   - `Cfinanciero` / `cfinancieros`
   - `Cdp` / `cdps`
3. Ejecutar:

```bash
php artisan migrate
```

## Consideración importante

Las migraciones nuevas tienen claves foráneas hacia tablas que ya existen en tu arquitectura. Si tu proyecto usa nombres de modelos distintos o alguna tabla aún no existe, ajusta esas relaciones antes de ejecutar `php artisan migrate`.

## Flujo previsto

1. Playwright extrae requerimientos y adjuntos desde Mesa de Ayuda.
2. Laravel importa el JSON usando `ImportarRequerimientosMesaAyudaService`.
3. El clasificador determina si el requerimiento corresponde a CDP.
4. Si corresponde, el flujo posterior genera expediente presupuestario y CDP borrador.
5. El usuario revisa y aprueba antes de cualquier respuesta oficial.

## Lo que todavía no incluye este ZIP

- Controladores.
- Rutas.
- Vistas Blade.
- Script Playwright.
- Seeder de estados.
- Generación Word/PDF del CDP.
- Automatización de respuesta en Mesa de Ayuda.

