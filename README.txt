Extracción de texto PDF para módulo contractual

Incluye:
- Migración para agregar:
  - texto_extraido
  - extraccion_estado
  - tiene_texto_extraible
- Servicio PdfTextExtractorService
- Controller DocumentoRevisionContractualController actualizado
- Modelo DocumentoRevisionContractual actualizado
- Snippets:
  - instalación de spatie/pdf-to-text
  - actualización del Prompt Builder

Flujo:
1. Usuario sube PDF
2. Se intenta extraer texto
3. Si funciona:
   - texto_extraido = contenido
   - extraccion_estado = EXTRAIDO
   - tiene_texto_extraible = true
4. Si no funciona:
   - texto_extraido = null
   - extraccion_estado = SIN_TEXTO o ERROR_EXTRACCION
   - tiene_texto_extraible = false

Pasos:
1. Copiar archivos respetando rutas
2. composer require spatie/pdf-to-text
3. asegurar binario pdftotext disponible
4. php artisan migrate
5. php artisan optimize:clear
