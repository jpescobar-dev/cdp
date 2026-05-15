# Fase 4 — Orquestación y trazabilidad de agentes

Esta fase agrega la capa base para registrar qué agente ejecuta cada tarea, detectar con precisión cuál falla y dejar trazabilidad técnica del proceso.

## Archivos incluidos

- `database/migrations/2026_05_04_000020_add_agent_fields_to_users_table.php`
- `database/migrations/2026_05_04_000021_create_agente_ejecuciones_table.php`
- `database/seeders/AgenteUsuariosSeeder.php`
- `app/Models/AgenteEjecucion.php`
- `app/Services/Agentes/AgenteEjecucionService.php`
- `app/Services/MesaAyuda/OrquestadorMesaAyudaService.php`
- `app/Jobs/MesaAyuda/ClasificarRequerimientoMesaAyudaJob.php`
- `config/agentes.php`
- `.env.example.agentes`

## Qué resuelve

1. Identifica cada agente con un código fijo.
2. Registra cada ejecución con UUID.
3. Registra entrada, salida, duración, estado y errores.
4. Permite saber qué agente falló.
5. Permite asociar la falla al requerimiento, expediente o borrador CDP.
6. Crea usuarios técnicos sin login para los agentes.

## Agentes técnicos creados

- `agente.orquestador`
- `agente.extractor.mesa_ayuda`
- `agente.importador_json`
- `agente.clasificador_cdp`
- `agente.lector_documentos`
- `agente.redactor_cdp`
- `agente.validador_cdp`
- `agente.respuesta_mesa_ayuda`

## Instalación

Copiar los archivos al proyecto Laravel y ejecutar:

```bash
php artisan migrate
php artisan db:seed --class=AgenteUsuariosSeeder
```

Luego revisar `.env.example.agentes` y copiar las variables necesarias al `.env` principal.

## Importante

Los agentes se registran como usuarios técnicos, pero no deben iniciar sesión ni actuar como usuarios humanos. La aprobación final del CDP debe seguir siendo humana.

## Uso recomendado

Todo agente debe ejecutarse a través del `AgenteEjecucionService` o del `OrquestadorMesaAyudaService`. No ejecutar servicios críticos por fuera si se requiere trazabilidad.

Ejemplo conceptual:

```php
$orquestador->clasificarRequerimiento($requerimiento, auth()->id());
```

Esto crea una fila en `agente_ejecuciones`, registra input/output y deja error detallado si falla.
