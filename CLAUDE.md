# CLAUDE.md — Proyecto CDP Laravel
# Corporación Administrativa del Poder Judicial

## Qué es este proyecto

Sistema web Laravel que automatiza el ciclo completo de un
Certificado de Disponibilidad Presupuestaria (CDP): desde que
un requirente llena un formulario, pasando por la captura y
clasificación de requerimientos desde Mesa de Ayuda, la
revisión y aprobación de borradores, hasta la gestión del
expediente presupuestario.

---

## Lo que está implementado y funcionando

### Autenticación y usuarios
- Login con Spatie Laravel-Permission (roles y permisos)
- `User` model sin campo `rut` (autenticación por email)
- Usuarios: `GET/POST /usuarios`, `DELETE /usuarios/{user}`
- Seeder inicial: `AdminUserSeeder`, `RolePermissionSeeder`

### Dashboard (`/dashboard`)
- `DashboardController@index` pasa estadísticas a la vista
- Línea de tiempo visual en 3 etapas: Solicitudes, Borradores, Expedientes
- Tarjetas con íconos Feather, estado visual por conteo (activo/alerta/vacío)
- Vista: `resources/views/dashboard.blade.php`
- Partial reutilizable: `resources/views/partials/dashboard-card.blade.php`

### Módulo CDP — Formulario del requirente (`/cdp/solicitudes`)
- El requirente llena un formulario, el sistema genera un PDF
- El PDF se descarga y se adjunta manualmente a Mesa de Ayuda
- **Controller:** `App\Http\Controllers\Cdp\CdpSolicitudController`
  - `index`, `create`, `store`, `show`, `descargar`
- **Model:** `App\Models\CdpSolicitud`
  - Relaciones: `belongsTo(User)`, `belongsTo(Ccosto)`, `belongsTo(Proyecto)`
  - Métodos: `nombreCdp()`, `montoFormateado()`
- **Service:** `App\Services\Cdp\GenerarPdfCdpService` (usa DomPDF)
- **Vista PDF:** `resources/views/cdp/solicitudes/pdf.blade.php`
- **Tabla:** `cdp_solicitudes`
- **Rutas** (prefix `cdp/`, name `cdp.`):
  - `GET  cdp/solicitudes`
  - `GET  cdp/solicitudes/create`
  - `POST cdp/solicitudes`
  - `GET  cdp/solicitudes/{solicitud}`
  - `GET  cdp/solicitudes/{solicitud}/descargar`

### Módulo Mesa de Ayuda (`/mesa-ayuda`)
**Extracciones**
- Playwright extrae requerimientos desde el sistema externo (frame ifrm1, modo solo lectura)
- `MesaAyudaExtraccionController`: `index`, `ejecutar`
- `EjecutarExtraccionMesaAyudaService`, `PlaywrightExtractorService`
- Tabla: `mesa_ayuda_extracciones`

**Requerimientos**
- `MesaAyudaRequerimientoController`: `index`, `show`, `reclasificar`, `verAdjunto`, `crearExpediente`
- Filtros: folio, clasificación, requiere_cdp
- `verAdjunto` sirve archivos desde `storage/app/` con `Content-Disposition: inline`
- `ClasificadorCdpService`: clasifica por scoring regex
- `CrearExpedientePresupuestarioDesdeRequerimientoService`
- Tabla: `mesa_ayuda_requerimientos` (SoftDeletes)
- Campos clave: `requiere_cdp` (bool), `clasificacion`, `expediente_presupuestario_id`
- **Regla:** solo se puede crear un expediente por requerimiento (check por `expediente_presupuestario_id`)

**Borradores CDP**
- `CdpBorradorController`: `index`, `show`, `update`, `generarDesdeRequerimiento`, `aprobar`, `rechazar`, `observar`
- **Regla:** única constraint DB en `mesa_ayuda_requerimiento_id` → un borrador por requerimiento
- Si ya existe, `generarDesdeRequerimiento` retorna el existente
- Estados: `borrador` → `borrador_editado` → `aprobado` / `rechazado` / `observado_usuario`
- El campo `advertencias` (JSON) funciona como audit trail/timeline cronológico
- Cada acción (`aprobar`, `rechazar`, `observar`) registra evento en `advertencias`
- La vista show tiene modales Bootstrap para aprobar (con confirmación) y rechazar (con motivo)
- Tabla: `cdp_borradores`
- **Rutas** (prefix `mesa-ayuda/`, name `mesa-ayuda.`):
  - `GET  mesa-ayuda/requerimientos`
  - `GET  mesa-ayuda/requerimientos/{requerimiento}`
  - `GET  mesa-ayuda/adjuntos/{adjunto}/ver`
  - `POST mesa-ayuda/requerimientos/{requerimiento}/reclasificar`
  - `POST mesa-ayuda/requerimientos/{requerimiento}/crear-expediente`
  - `POST mesa-ayuda/requerimientos/{requerimiento}/generar-cdp-borrador`
  - `GET  mesa-ayuda/cdp-borradores`
  - `GET  mesa-ayuda/cdp-borradores/{borrador}`
  - `PATCH mesa-ayuda/cdp-borradores/{borrador}`
  - `POST mesa-ayuda/cdp-borradores/{borrador}/aprobar`
  - `POST mesa-ayuda/cdp-borradores/{borrador}/rechazar`
  - `POST mesa-ayuda/cdp-borradores/{borrador}/observar`

### Módulo Expedientes Presupuestarios (`/presupuesto/expedientes`)
- `ExpedienteController`: `index`, `create`, `store`, `show`
- `ExpedienteWorkflowController`: `cambiarEstado`
- `ExpedienteWorkflowService`, `TareaWorkflowService`
- Flujo de estados: `Ingresado → En revisión → Aprobado → Emitido`
- Las transiciones están en tabla `transiciones_estados` con seeders
- Historial de cambios en `expediente_historial`
- **Model:** `App\Models\Presupuesto\ExpedientePresupuestario` (SoftDeletes)
- Sub-modelos: `ExpedienteHistorial`, `ExpedienteTarea`, `ExpedienteObservacion`, `ExpedienteAdjunto`
- **Tablas:** `expedientes_presupuestarios` + subtablas de historial/tareas/observaciones/adjuntos
- **Rutas** (prefix `presupuesto/`, name `presupuesto.`):
  - `GET  presupuesto/expedientes`
  - `GET  presupuesto/expedientes/create`
  - `POST presupuesto/expedientes`
  - `GET  presupuesto/expedientes/{expediente}`
  - `POST presupuesto/expedientes/{expediente}/cambiar-estado`

### Modelos de soporte
| Modelo | Tabla | PK | Nota |
|---|---|---|---|
| `Funcionario` | `funcionarios` | `rut` (string) | `$incrementing = false`, SoftDeletes |
| `Ccosto` | `ccostos` | `ccosto` (string) | `$incrementing = false` |
| `Cfinanciero` | `cfinancieros` | string | |
| `Estado` | `estados` | id | campo `tabla_referencia` para filtrar por módulo |
| `TransicionEstado` | `transiciones_estados` | id | define flujos válidos entre estados |
| `Proyecto` | `proyectos` | id | |
| `AgenteEjecucion` | `agente_ejecuciones` | id | trazabilidad de todos los agentes |

### Orquestación de agentes
- Servicio principal: `OrquestadorMesaAyudaService`
- Trazabilidad en `agente_ejecuciones`
- Todo agente nuevo debe registrarse en `agente_ejecuciones`

### Navegación (topnav)
- Layout base: `layouts.theme.app` — todas las vistas lo extienden
- Menú **Mesa de Ayuda**: Extracciones, Requerimientos, Borradores CDP
- Menú **Presupuesto**: Solicitudes CDP, Expedientes
- Menú **Sistema**: Usuarios (solo con permiso)
- Los items del menú se muestran solo si la ruta existe (`Route::has(...)`)

---

## Decisiones de arquitectura ya tomadas

- `solicitante_rut` y `ccosto` en `expedientes_presupuestarios` son **nullable**
  (FKs a `funcionarios` y `ccostos` eliminadas — tablas vacías en esta etapa)
- `User` no tiene campo `rut`; para buscar el `Funcionario` asociado usar `email`
- `advertencias` en `cdp_borradores` es un JSON array que actúa como log de eventos
- Un borrador por requerimiento: unique constraint en `cdp_borradores.mesa_ayuda_requerimiento_id`
- Los adjuntos de Mesa de Ayuda se sirven con `Storage::response()` para visualización inline

---

## Lo que falta implementar

### Fase B — Extracción de texto de adjuntos
Cuando un requerimiento CDP tiene adjuntos en
`storage/app/mesa-ayuda/adjuntos/{folio}/`, extraer el texto
para preparar el input de la IA (Fase C).

Formatos a soportar: PDF, imagen (JPG/PNG vía OCR), Word (.docx), Excel (.xlsx).
No usar IA. Usar librerías PHP — verificar `composer.json` antes de proponer instalaciones.

Archivos a crear:
- Migration: agregar `texto_extraido` (longText, nullable) a `mesa_ayuda_adjuntos`
- `App\Services\MesaAyuda\ExtraerTextoAdjuntosService`
- `App\Jobs\ExtraerTextoAdjuntosJob`
- Integrar al orquestador como: `agente.extractor_texto_adjuntos`

### Fase C — Agente IA: sugerencia de cuenta presupuestaria
Se ejecuta después de Fase B. Toma glosa + texto de adjuntos
+ catálogo completo (`catalogos`, 156 registros) y llama a
la API de OpenAI (gpt-4o) para sugerir 1–3 cuentas.

Formato de respuesta esperado del modelo:
```json
{
  "sugerencias": [
    {
      "catalogo": "2204001000",
      "nombre": "Materiales de Oficina",
      "confianza": "alta",
      "justificacion": "texto breve"
    }
  ],
  "observacion_general": "texto opcional"
}
```

La clave va en `.env` como `OPENAI_API_KEY`.
Usar `Http::` de Laravel (no instalar SDK de OpenAI sin verificar `composer.json`).

Archivos a crear:
- Migration: tabla `cdp_sugerencias_ia`
  (`requerimiento_id`, `sugerencias_json`, `observacion`, `tokens_usados`, `modelo`, `created_at`)
- `App\Models\CdpSugerenciaIa`
- `App\Services\MesaAyuda\SugerirCuentaPresupuestariaService`
- `App\Jobs\SugerirCuentaPresupuestariaJob`
- Integrar al orquestador como: `agente.sugeridor_cuenta`

### Fase E — Generación del PDF CDP institucional
Al aprobar el borrador, generar el PDF final con plantilla
institucional. Guardar en:
`storage/app/cdp/emitidos/{folio}/CDP_{folio}_{fecha}.pdf`
Actualizar estado del expediente a `pdf_generado`.

Archivos a crear:
- `App\Services\Cdp\EmitirCdpPdfService`
- `App\Jobs\EmitirCdpPdfJob`
- Integrar al orquestador como: `agente.emisor_pdf_cdp`

### Fase F — Playwright: carga a sistema de firma electrónica
Subir el PDF generado al sistema externo JSP de firma:
`https://funpfirmagob.pjud.cl/PFIRMAFUNWEB/jsp/Login/Login.jsp`

Credenciales en `.env`: `FIRMA_USER`, `FIRMA_PASSWORD`.
Solo sube documentos — no firma ni modifica nada existente.
Si falla: estado `error_carga_firma`, registrar en `agente_ejecuciones`.

Antes de implementar: sesión de exploración headless=false
para mapear selectores de la aplicación JSP.

Archivos a crear:
- `tests-playwright/explorar-firma-electronica.cjs`
- `tools/firma-electronica/src/cargar-cdp.js`
- `App\Services\Cdp\CargarCdpFirmaElectronicaService`
- `App\Jobs\CargarCdpFirmaElectronicaJob`
- Integrar al orquestador como: `agente.cargador_firma_electronica`

---

## Restricciones para todo el proyecto

- **Framework:** Laravel 10, PHP 8.1
- **Layout:** todas las vistas extienden `layouts.theme.app`
- **Íconos:** Feather Icons vía `data-feather="..."` — el `color` de stroke se hereda del elemento padre (no del `<i>`), configurar `color:` en el div contenedor
- Verificar **siempre** `composer.json` antes de proponer instalar una librería nueva
- Seguir el patrón de servicios y jobs existente
- Todo agente nuevo se registra en `agente_ejecuciones`
- Usar el orquestador existente `OrquestadorMesaAyudaService` como punto de entrada
- Mesa de Ayuda permanece en **modo solo lectura** hasta validar el flujo completo
- Los CDPs los procesa solo el área de finanzas y presupuesto
- No romper lo ya implementado — revisar rutas y modelos antes de crear nuevos

## Variables .env relevantes

```
OPENAI_API_KEY=          # Fase C
FIRMA_USER=              # Fase F
FIRMA_PASSWORD=          # Fase F
```
