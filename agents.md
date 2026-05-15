# Contexto del proyecto — CDP Laravel

## Estado actual (ya implementado, no tocar)
- Extracción Playwright desde Mesa de Ayuda (frame ifrm1)
- Importación JSON a tablas: `mesa_ayuda_extracciones`, 
  `mesa_ayuda_requerimientos`, `mesa_ayuda_historial`, 
  `mesa_ayuda_adjuntos`
- Clasificación por tipificación: campo `clasificacion` en 
  `mesa_ayuda_requerimientos` con valor 
  `certificado_disponibilidad_presupuestaria`
- Orquestación de agentes con trazabilidad en 
  `agente_ejecuciones`
- UI básica: menú, extracciones, botón conectar

## Lo que viene — orden de implementación

### Fase A — Formulario CDP para el requirente
Construir un formulario web en Laravel que el requirente 
llena antes de ingresar su requerimiento a Mesa de Ayuda.

Propósito: estructurar los datos desde el origen para 
mejorar la calidad del input que luego procesa la IA.

El formulario debe:
- Tener campos: requirente (nombre, RUT, unidad), 
  descripción del gasto (glosa), proveedor, monto estimado, 
  y sección para adjuntar documentos (cotizaciones, 
  presupuestos, etc.)
- Validar todos los campos en Laravel antes de procesar
- Al finalizar, generar un PDF con el formato institucional 
  del CDP usando el formato que se proporcionará
- El PDF generado es para que el requirente lo descargue 
  y lo adjunte manualmente a su requerimiento en 
  Mesa de Ayuda — la aplicación Laravel NO accede a 
  Mesa de Ayuda en este paso
- No requiere autenticación en Mesa de Ayuda ni credenciales 
  de ningún sistema externo

Archivos a crear:
- Migration: `cdp_solicitudes` con campos del formulario
- Model: `CdpSolicitud`
- Controller: `CdpSolicitudController`
- Vista: formulario con validación client-side y server-side
- Service: `GenerarPdfCdpService` usando la librería 
  que ya esté disponible en el proyecto (verificar 
  composer.json antes de instalar algo nuevo)
- Ruta: `cdp/solicitudes`

### Fase B — Extracción de texto de adjuntos
Cuando un requerimiento clasificado como CDP tiene adjuntos 
descargados en `storage/app/mesa-ayuda/adjuntos/{folio}/`, 
extraer el texto de cada archivo para dejarlo disponible 
para el paso de IA.

Formatos a soportar (restringir a estos en el formulario 
de la Fase A también):
- PDF: extracción directa de texto
- Imagen (JPG, PNG): OCR
- Word (.docx): extracción de texto
- Excel (.xlsx): extracción de contenido de celdas 
  relevantes

El texto extraído se guarda en la tabla 
`mesa_ayuda_adjuntos` en un campo nuevo `texto_extraido`.

No usar IA en este paso. Usar librerías PHP estándar.
Verificar qué hay disponible en composer.json antes de 
proponer instalaciones.

Archivos a crear:
- Migration: agregar `texto_extraido` (longText, nullable) 
  a `mesa_ayuda_adjuntos`
- Service: `ExtraerTextoAdjuntosService`
- Job: `ExtraerTextoAdjuntosJob`
- Integrar al orquestador existente como nuevo agente: 
  `agente.extractor_texto_adjuntos`

### Fase C — Agente IA: sugerencia de cuenta presupuestaria
Este es el paso de IA del flujo. Se ejecuta después de la 
Fase B, cuando ya existe texto extraído de los adjuntos.

El agente debe:
- Tomar la glosa del requerimiento + texto extraído de 
  los adjuntos del folio
- Tomar el catálogo completo desde la tabla `catalogos` 
  (156 registros con código, nombre y descripción)
- Llamar a la API de OpenAI (modelo gpt-4o) con un prompt 
  que incluya todo ese contexto
- El prompt debe pedir al modelo que devuelva exactamente 
  un JSON con este formato:
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
- Devolver entre 1 y 3 sugerencias ordenadas por confianza
- Guardar el resultado en tabla nueva `cdp_sugerencias_ia`
- Registrar la ejecución en `agente_ejecuciones` 
  como todos los demás agentes

La clave de OpenAI va en `.env` como `OPENAI_API_KEY`.
Usar el cliente HTTP de Laravel (no instalar SDK de 
OpenAI si no está ya en composer.json — verificar primero).

Archivos a crear:
- Migration: `cdp_sugerencias_ia` con campos 
  `requerimiento_id`, `sugerencias_json`, `observacion`, 
  `tokens_usados`, `modelo`, `created_at`
- Model: `CdpSugerenciaIa`
- Service: `SugerirCuentaPresupuestariaService`
- Job: `SugerirCuentaPresupuestariaJob`
- Integrar al orquestador como: `agente.sugeridor_cuenta`

### Fase D — UI de revisión y aprobación para finanzas
Pantalla donde el funcionario de finanzas ve el 
requerimiento CDP con toda la información y la sugerencia 
de la IA, y puede aprobar o rechazar.

La pantalla debe mostrar:
- Datos del requerimiento (folio, solicitante, glosa, 
  historial)
- Adjuntos descargados con opción de visualización
- Las sugerencias de cuenta de la IA con su justificación 
  y nivel de confianza, destacando la de mayor confianza
- Formulario para que el funcionario seleccione la cuenta 
  final (puede ser una de las sugeridas u otra del 
  catálogo), complete los campos faltantes del CDP 
  (monto definitivo, partida, etc.) y escriba 
  observaciones si rechaza
- Botones: Aprobar / Rechazar
- El rechazo debe permitir devolver con comentario

Al aprobar, el registro queda en estado `aprobado` y 
gatilla la Fase E.

Archivos a crear:
- Migration: agregar campos de aprobación a 
  `mesa_ayuda_requerimientos`: `cuenta_catalogo_final`, 
  `monto_definitivo`, `aprobado_por`, `fecha_aprobacion`, 
  `observacion_rechazo`, `estado_cdp`
- Controller: `CdpRevisionController`
- Vistas: index (lista de CDPs pendientes) + show 
  (detalle con formulario de aprobación)
- Rutas: `cdp/revision`

### Fase E — Generación del PDF CDP institucional
Cuando el funcionario aprueba, generar el PDF del CDP 
con la plantilla institucional predefinida.

El PDF debe usar los datos aprobados: requirente, glosa, 
cuenta presupuestaria seleccionada, monto, proveedor, 
fecha, nombre del funcionario que aprueba.

Guardar el PDF en:
`storage/app/cdp/emitidos/{folio}/CDP_{folio}_{fecha}.pdf`

Actualizar el estado del requerimiento a `pdf_generado`.

Archivos a crear:
- Service: `EmitirCdpPdfService`
- Job: `EmitirCdpPdfJob`
- Integrar al orquestador como: `agente.emisor_pdf_cdp`

### Fase F — Playwright: carga a sistema de firma externa
Automatizar la carga del PDF generado al sistema externo 
de firma electrónica avanzada.

Sistema de firma: 
https://funpfirmagob.pjud.cl/PFIRMAFUNWEB/jsp/Login/Login.jsp

Es una aplicación web JSP con login estándar. 
Playwright debe:
1. Hacer login con credenciales desde `.env`
   (`FIRMA_USER`, `FIRMA_PASSWORD`)
2. Navegar a la sección de carga de documentos
3. Subir el PDF del CDP generado en Fase E
4. Verificar que el documento quedó cargado correctamente
5. Registrar el resultado (éxito o error) en 
   `agente_ejecuciones`
6. Actualizar el estado del requerimiento a 
   `pendiente_firma`

IMPORTANTE — seguridad:
- Las credenciales de firma van SOLO en `.env`, 
  nunca hardcodeadas
- Este agente solo sube documentos, no firma, no aprueba, 
  no modifica documentos existentes
- Si falla la carga, registrar el error y dejar el estado 
  en `error_carga_firma` para reintento manual

Antes de implementar este paso, hacer una sesión de 
exploración con Playwright en modo headless=false para 
mapear la estructura de la aplicación de firma: 
identificar el selector del campo de carga, el botón 
de subida y la confirmación de éxito.

Archivos a crear:
- Script exploración: 
  `tests-playwright/explorar-firma-electronica.cjs`
- Script carga: 
  `tools/firma-electronica/src/cargar-cdp.js`
- Service: `CargarCdpFirmaElectronicaService`
- Job: `CargarCdpFirmaElectronicaJob`
- Integrar al orquestador como: 
  `agente.cargador_firma_electronica`

## Restricciones generales para todo el proyecto

- Framework: Laravel (versión la que ya está instalada)
- Verificar SIEMPRE composer.json antes de proponer 
  instalar una librería nueva
- Seguir la estructura de servicios y jobs existente 
  (ver Fases 4-9 ya implementadas)
- Todo agente nuevo se registra en `agente_ejecuciones`
- Usar el orquestador existente 
  `OrquestadorMesaAyudaService` como punto de entrada
- Las vistas usan el layout `layouts.theme.app`
- Los CDPs solo los procesa el área de finanzas y 
  presupuesto, no otros usuarios
- Modo solo lectura en Mesa de Ayuda se mantiene hasta 
  que esté validado el flujo completo
- No modificar las Fases 1-9 ya implementadas salvo 
  para integrar nuevos agentes al orquestador

## Variables .env que se agregarán