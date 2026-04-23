<?php

namespace App\Services\Contractual;

use App\Models\RevisionContractual;
use Illuminate\Support\Facades\Storage;

class PromptRevisionContractualBuilderService
{
    protected string $basePath = 'prompts/contractual/';

    public function build(RevisionContractual $revision): string
    {
        $partes = [
            $this->read('01_contexto.md'),
            $this->read('02_objetivo.md'),
            $this->read('03_datos.md'),
            $this->read('04_restricciones.md'),
            $this->read('05_estilo.md'),
            $this->read('06_formato_salida.md'),
        ];

        $contenidoBase = implode("\n\n", $partes);
        $datosRevision = $this->buildDatosRevision($revision);

        return $contenidoBase . "\n\n" . $datosRevision;
    }

    protected function read(string $file): string
    {
        $path = $this->basePath . $file;

        if (!Storage::disk('local')->exists($path)) {
            throw new \RuntimeException("No se encontró el archivo de instrucciones: {$path}");
        }

        $contenido = Storage::disk('local')->get($path);

        if (!is_string($contenido) || trim($contenido) === '') {
            throw new \RuntimeException("El archivo de instrucciones está vacío o no es legible: {$path}");
        }

        return $contenido;
    }

    protected function buildDatosRevision(RevisionContractual $revision): string
    {
        $revision->load('documentos');

        $documentos = $revision->documentos->map(function ($doc) {
            $tipo = $doc->tipo_documento ?: 'SIN TIPO';
            $estadoExtraccion = $doc->extraccion_estado ?: 'PENDIENTE';

            if ($doc->tiene_texto_extraible && !empty($doc->texto_extraido)) {
                $texto = trim($doc->texto_extraido);

                // Limitar tamaño por documento para no reventar el prompt
                $texto = mb_substr($texto, 0, 15000);

                return <<<TXT
### DOCUMENTO: {$doc->nombre_original}
Tipo: {$tipo}
Estado extracción: {$estadoExtraccion}

{$texto}
TXT;
            }

            return <<<TXT
### DOCUMENTO: {$doc->nombre_original}
Tipo: {$tipo}
Estado extracción: {$estadoExtraccion}

No fue posible extraer texto legible del documento.
TXT;
        })->implode("\n\n");

        if (blank($documentos)) {
            $documentos = '- No existen documentos cargados en la revisión.';
        }

        $descripcion = $revision->descripcion ?: 'Sin descripción';

        return <<<TXT
# DOCUMENTACIÓN A ANALIZAR

## Identificación de la revisión
- ID revisión: {$revision->id}
- Título: {$revision->titulo}
- Descripción: {$descripcion}

## Documentos disponibles
{$documentos}

## Instrucción final
Realiza la revisión contractual preliminar aplicando estrictamente las reglas anteriores.
Trabaja solo con la información disponible.
Si faltan antecedentes, indícalo expresamente.
TXT;
    }
}