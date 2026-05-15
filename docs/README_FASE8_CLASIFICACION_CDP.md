# Fase 8 - Clasificación CDP de requerimientos Mesa de Ayuda

Esta fase agrega una clasificación formal en Laravel para los registros ya importados en `mesa_ayuda_requerimientos`.

## Archivos incluidos

```text
app/Services/MesaAyuda/ClasificarRequerimientoMesaAyudaService.php
app/Console/Commands/ClasificarRequerimientosMesaAyudaCommand.php
app/Jobs/ClasificarRequerimientoMesaAyudaJob.php
```

## Qué hace

Clasifica cada requerimiento como:

```text
certificado_disponibilidad_presupuestaria
posible_certificado_disponibilidad_presupuestaria
otro
```

Además actualiza:

```text
clasificacion
requiere_cdp
confianza_clasificacion
score_clasificacion
evidencias_clasificacion
destino_flujo
procesar_automaticamente
motivo_routing
estado_id, si existe el estado en la tabla estados
```

## Comandos

Clasificar pendientes:

```bash
php artisan mesa-ayuda:clasificar --pendientes
```

Clasificar todos nuevamente:

```bash
php artisan mesa-ayuda:clasificar --all
```

Clasificar un folio:

```bash
php artisan mesa-ayuda:clasificar --folio=7954868
```

Clasificar por ID interno:

```bash
php artisan mesa-ayuda:clasificar --id=1
```

Con límite:

```bash
php artisan mesa-ayuda:clasificar --pendientes --limit=10
```

## Reglas principales

La señal fuerte es:

```text
Certificados de disponibilidad presupuestaria
```

Señales complementarias:

```text
CDP
solicito CDP
certificado de disponibilidad
disponibilidad presupuestaria
cotización
presupuesto
orden de compra
OC
```

La palabra `presupuesto` sola no debería mandar el flujo a CDP, solo suma evidencia secundaria.

## Resultado esperado

Para los requerimientos revisados en Mesa de Ayuda, especialmente los que vienen con tipificación `Certificados De Disponibilidad Presupuestaria`, el resultado esperado es:

```text
clasificacion = certificado_disponibilidad_presupuestaria
requiere_cdp = true
confianza_clasificacion = alta
destino_flujo = agente_presupuestario_cdp
procesar_automaticamente = true
```

## Siguiente fase

Después de clasificar, corresponde crear el flujo que transforma un requerimiento CDP en expediente presupuestario interno.
