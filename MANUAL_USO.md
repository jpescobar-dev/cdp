# Manual de Uso — Sistema CDP (Certificado de Disponibilidad Presupuestaria)

## ¿Qué hace este sistema?

El sistema automatiza el ciclo completo de un Certificado de Disponibilidad Presupuestaria (CDP): desde que un funcionario necesita acreditar disponibilidad de fondos, pasando por la captura y clasificación automática de requerimientos desde Mesa de Ayuda, hasta la generación del borrador del certificado y su seguimiento como expediente presupuestario.

---

## Ejemplo de referencia

A lo largo de este manual se usa el siguiente caso real:

> **Juan Pérez**, administrativo de la **Unidad de Administración de Coyhaique**, necesita contratar el servicio de **tala de árbol álamo en el interior del recinto de Viviendas Judiciales de Gral. Parra 145**, con el proveedor **Servicios Forestales Patagonia Ltda.**, por un valor de **$952.000 (CLP)**.

---

## Módulos del sistema

El sistema tiene cuatro módulos activos, cada uno con su URL:

| Módulo | URL | Quién lo usa |
|---|---|---|
| Formulario CDP (requirente) | `/cdp/solicitudes` | El funcionario que necesita el CDP |
| Extracciones Mesa de Ayuda | `/mesa-ayuda/extracciones` | Analista de finanzas |
| Requerimientos | `/mesa-ayuda/requerimientos` | Analista de finanzas |
| Expedientes Presupuestarios | `/presupuesto/expedientes` | Analista / jefe de presupuesto |

---

## Flujo completo paso a paso

```
[Juan Pérez]                    [Analista Finanzas]             [Mesa de Ayuda]
     │                                  │                              │
     ▼                                  │                              │
1. Llena formulario CDP                 │                              │
   /cdp/solicitudes/create              │                              │
     │                                  │                              │
     ▼                                  │                              │
2. Descarga PDF generado                │                              │
     │                                  │                              │
     ▼                                  │                              │
3. Adjunta PDF a requerimiento ─────────────────────────────────────► │
   en Mesa de Ayuda                     │                    4. Requerimiento
     │                                  │                       ingresa al sistema
     │                                  ▼
     │                        5. Extracción automática
     │                           /mesa-ayuda/extracciones
     │                                  │
     │                                  ▼
     │                        6. Clasificación automática
     │                           /mesa-ayuda/requerimientos
     │                                  │
     │                                  ▼
     │                        7. Creación de expediente
     │                           + borrador CDP
     │                                  │
     │                                  ▼
     │                        8. Seguimiento del expediente
     │                           /presupuesto/expedientes
```

---

## Paso 1 — El requirente llena el formulario CDP

**Quién:** Juan Pérez  
**URL:** `/cdp/solicitudes/create`

Juan ingresa al sistema y completa el formulario en tres secciones:

### Sección 1: Datos del requirente

| Campo | Ejemplo |
|---|---|
| Nombre completo | Juan Pérez Soto |
| RUT | 12.345.678-9 |
| Unidad / Departamento | Unidad de Administración Coyhaique |
| Centro de costo | (seleccionar de la lista o ingresar código) |
| N° Requerimiento | (opcional — si ya tiene folio en Mesa de Ayuda) |

### Sección 2: Datos del gasto

| Campo | Ejemplo |
|---|---|
| Glosa (descripción del gasto) | Tala de árbol álamo en interior de recinto de Viviendas Judiciales de Gral. Parra 145, Coyhaique. Valor servicio $952.000. |
| Proveedor | Servicios Forestales Patagonia Ltda. |
| Monto estimado | 952000 |
| Moneda | CLP (Pesos) |
| Tipo de gasto | GO — Gasto Operacional |
| Clasificación del gasto | Transitorio |
| Proyecto | (dejar en blanco si es gasto operacional) |

### Sección 3: Documentos de respaldo

Juan adjunta la cotización del proveedor: `Cotizacion_Patagonia_952000.pdf`

Formatos aceptados: PDF, JPG, PNG, DOCX, XLSX — máximo 5 archivos de 10 MB cada uno.

Luego hace clic en **"Enviar y generar PDF"**.

---

## Paso 2 — Descarga el PDF generado

Tras enviar el formulario, el sistema redirige a la pantalla de confirmación (`/cdp/solicitudes/{id}`) con un mensaje de éxito y un botón **"Descargar PDF"**.

Juan descarga el archivo: `CDP_solicitud_2026_001.pdf`

El PDF contiene todos los datos estructurados en formato institucional, listo para adjuntarse al requerimiento.

> **Importante:** El sistema genera el PDF pero NO lo envía a Mesa de Ayuda. Juan debe adjuntarlo manualmente a su requerimiento.

---

## Paso 3 — Juan adjunta el PDF a su requerimiento en Mesa de Ayuda

Juan ingresa a Mesa de Ayuda, abre o crea su requerimiento y adjunta el PDF descargado. Esto ocurre fuera del sistema CDP.

---

## Paso 4 — El analista ejecuta la extracción

**Quién:** Analista de finanzas  
**URL:** `/mesa-ayuda/extracciones`

El analista ve la pantalla de extracciones y hace clic en el botón **"Conectar y extraer"**. El sistema usa Playwright para conectarse a Mesa de Ayuda en modo solo lectura, capturar los requerimientos activos y guardarlos como JSON.

La tabla muestra el resultado de cada extracción:

| Campo | Descripción |
|---|---|
| Inicio / Término | Cuándo comenzó y terminó la extracción |
| Estado | `completado`, `error` o `en proceso` |
| Detectados | Total de requerimientos encontrados en Mesa de Ayuda |
| Importados | Cuántos se guardaron correctamente en la base de datos |
| Errores | Cuántos fallaron durante la importación |

Tras la extracción, el requerimiento de Juan Pérez queda registrado con folio **7958334**.

---

## Paso 5 — El analista revisa y clasifica los requerimientos

**URL:** `/mesa-ayuda/requerimientos`

El analista ve la bandeja de requerimientos. Puede filtrar por:
- **Folio:** buscar `7958334`
- **Clasificación:** CDP, Posible CDP, Otro
- **Requiere CDP:** Sí / No

Al buscar el folio 7958334, el analista encuentra el requerimiento de Juan y hace clic en **"Ver"**.

### Vista detalle del requerimiento (`/mesa-ayuda/requerimientos/{id}`)

La pantalla muestra cuatro secciones:

**Izquierda (datos principales):**
- **Cabecera:** fecha, componente, tipo, tribunal, solicitante
- **Observación principal:** el texto completo del requerimiento con la descripción de la tala del árbol
- **Historial externo:** todos los estados y acciones registrados en Mesa de Ayuda
- **Adjuntos capturados:** lista de archivos adjuntos al requerimiento, con botón de ojo para visualizar cada uno directamente en el navegador

**Derecha (panel de acción):**
- **Clasificación:** el sistema muestra si fue clasificado automáticamente como CDP, con nivel de confianza y score
- Botón **"Reclasificar"** para volver a ejecutar el clasificador si el resultado no es correcto
- Botón **"Crear expediente presupuestario"** (disponible solo si `requiere_cdp = Sí`)
- Botón **"Generar borrador CDP"** (disponible solo si `requiere_cdp = Sí`)

El analista verifica que el requerimiento 7958334 está clasificado como **CDP** con confianza **alta**. Procede a crear el expediente.

---

## Paso 6 — El analista crea el expediente presupuestario

Desde el panel derecho del detalle del requerimiento, el analista hace clic en **"Crear expediente presupuestario"**.

El sistema genera automáticamente el expediente con correlativo **EXP-2026-0001**, tomando la glosa del requerimiento original ("Tala de árbol álamo...") como descripción.

El expediente queda en estado inicial con los campos de cuenta presupuestaria y monto en `PENDIENTE` — el analista los completará en el expediente.

---

## Paso 7 — El analista genera el borrador CDP

De vuelta en el panel del requerimiento, hace clic en **"Generar borrador CDP"**.

> El sistema garantiza que solo se puede generar un borrador por requerimiento. Si ya existe uno, redirige al borrador existente en lugar de crear uno nuevo.

El sistema redirige al borrador en `/mesa-ayuda/cdp-borradores/{id}` con los campos pre-llenados:

| Campo | Valor generado |
|---|---|
| Nombre iniciativa | Tala de árbol álamo en interior de recinto... |
| N° requerimiento | 7958334 |
| Fecha emisión | 14-05-2026 |
| Carácter gasto | TRANSITORIO |
| Validez | AÑO 2026 |
| CF | 14 |
| Unidad ejecutora | COYHAIQUE |

### Completar el borrador

El analista llena los campos que el sistema no pudo completar automáticamente:

| Campo faltante | Valor a completar |
|---|---|
| Número CDP | (asignado por el sistema de presupuesto) |
| Cuenta presupuestaria | 2203001000 |
| Monto imp. incluido | 952.000 |
| ST | (subtítulo presupuestario) |
| Denominación | Servicios de mantención y reparación |

Hace clic en **"Guardar cambios"** y luego en **"Aprobar borrador"** para marcar el documento como revisado.

---

## Paso 8 — Seguimiento del expediente presupuestario

**URL:** `/presupuesto/expedientes`

El analista o jefe de presupuesto ve todos los expedientes activos. Puede hacer clic en **"Ver"** para abrir el expediente EXP-2026-0001.

### Vista detalle del expediente

**Panel izquierdo:**
- Datos del expediente: estado, solicitante, responsable, cuenta, monto, glosa
- Historial completo de cambios de estado con fecha, usuario y comentario

**Panel derecho — Cambio de estado:**

El flujo de estados es lineal:

```
Ingresado → En revisión → Aprobado → Emitido
```

El jefe de presupuesto revisa el expediente y hace clic en **"Pasar a En revisión"**, escribe un comentario opcional y confirma. Cada cambio queda registrado en el historial con fecha y usuario.

---

## Listado de mis solicitudes CDP (vista del requirente)

**URL:** `/cdp/solicitudes`

Juan Pérez puede ver todas sus solicitudes anteriores. La tabla muestra estado, fecha y un enlace para volver a descargar el PDF de cada solicitud.

---

## Accesos directos por rol

### Analista de finanzas
- Extracciones: `/mesa-ayuda/extracciones`
- Requerimientos: `/mesa-ayuda/requerimientos`
- Borradores CDP: accesible desde cada requerimiento

### Requirente / funcionario
- Nueva solicitud CDP: `/cdp/solicitudes/create`
- Mis solicitudes: `/cdp/solicitudes`

### Jefe de presupuesto
- Expedientes: `/presupuesto/expedientes`

---

## Preguntas frecuentes

**¿Qué pasa si el requerimiento no se clasifica como CDP?**  
Aparece sin badge o con clasificación "Otro". El analista puede usar el botón "Reclasificar" para volver a ejecutar la clasificación automática.

**¿Puedo generar más de un borrador CDP para el mismo requerimiento?**  
No. El sistema bloquea la creación de un segundo borrador y redirige al existente.

**¿El PDF del formulario se envía automáticamente a Mesa de Ayuda?**  
No. El requirente debe descargarlo y adjuntarlo manualmente a su requerimiento en Mesa de Ayuda.

**¿Qué archivos puedo adjuntar en el formulario CDP?**  
PDF, JPG, PNG, DOCX y XLSX. Máximo 5 archivos de 10 MB cada uno.

**¿Cómo veo los archivos adjuntos de un requerimiento capturado desde Mesa de Ayuda?**  
En el detalle del requerimiento, la sección "Adjuntos capturados" muestra cada archivo con un botón de ojo que abre el documento en una nueva pestaña del navegador.
