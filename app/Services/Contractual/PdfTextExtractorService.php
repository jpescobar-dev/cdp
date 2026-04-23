<?php

namespace App\Services\Contractual;

use Illuminate\Support\Facades\Storage;
use Spatie\PdfToText\Pdf;

class PdfTextExtractorService
{
    protected string $pdfToTextBinary = 'C:\\poppler\\Library\\bin\\pdftotext.exe';

    public function extractFromPublicPath(string $relativePath): array
    {
        $absolutePath = Storage::disk('public')->path($relativePath);

        \Log::info('Ruta PDF para extracción', [
            'relativePath' => $relativePath,
            'absolutePath' => $absolutePath,
            'exists' => file_exists($absolutePath),
            'binary' => $this->pdfToTextBinary,
        ]);

        if (!file_exists($absolutePath)) {
            return [
                'texto' => null,
                'estado' => 'ERROR_EXTRACCION',
                'tiene_texto_extraible' => false,
                'mensaje' => 'El archivo no existe en disco.',
            ];
        }

        try {
            $texto = (new Pdf($this->pdfToTextBinary))
                ->setPdf($absolutePath)
                ->text();

            $texto = is_string($texto) ? trim($texto) : null;

            if (!$texto || mb_strlen($texto) < 30) {
                return [
                    'texto' => null,
                    'estado' => 'SIN_TEXTO',
                    'tiene_texto_extraible' => false,
                    'mensaje' => 'No fue posible extraer texto útil del PDF.',
                ];
            }

            return [
                'texto' => $texto,
                'estado' => 'EXTRAIDO',
                'tiene_texto_extraible' => true,
                'mensaje' => 'Texto extraído correctamente.',
            ];
        } catch (\Throwable $e) {
            \Log::error('Error extracción PDF', [
                'archivo' => $absolutePath,
                'mensaje' => $e->getMessage(),
            ]);

            return [
                'texto' => null,
                'estado' => 'ERROR_EXTRACCION',
                'tiene_texto_extraible' => false,
                'mensaje' => $e->getMessage(),
            ];
        }
    }
}