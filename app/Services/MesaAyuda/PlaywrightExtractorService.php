<?php

namespace App\Services\MesaAyuda;

use App\Models\MesaAyudaExtraccion;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Symfony\Component\Process\Process;

class PlaywrightExtractorService
{
    public function ejecutar(MesaAyudaExtraccion $extraccion): string
    {
        $baseDir = config('mesa_ayuda.storage.base_dir');
        $outputDir = $baseDir . DIRECTORY_SEPARATOR . 'extracciones' . DIRECTORY_SEPARATOR . $extraccion->id;
        $jsonPath = $outputDir . DIRECTORY_SEPARATOR . 'requerimientos_pendientes.json';

        File::ensureDirectoryExists($outputDir . DIRECTORY_SEPARATOR . 'adjuntos');

        $env = [
            'MESA_URL' => config('mesa_ayuda.url'),
            'MESA_USER' => config('mesa_ayuda.username'),
            'MESA_PASSWORD' => config('mesa_ayuda.password'),
            'HEADLESS' => filter_var(config('mesa_ayuda.headless'), FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false',
            'OUTPUT_DIR' => $outputDir,
            'EXTRACTION_ID' => (string) $extraccion->id,
        ];

        $process = new Process([
            config('mesa_ayuda.playwright.node_binary'),
            config('mesa_ayuda.playwright.script_path'),
        ], base_path(), $env);

        $process->setTimeout((int) config('mesa_ayuda.playwright.timeout_seconds'));
        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException(trim($process->getErrorOutput() ?: $process->getOutput()));
        }

        if (! File::exists($jsonPath)) {
            throw new RuntimeException("El extractor terminó, pero no generó el JSON esperado: {$jsonPath}");
        }

        return $jsonPath;
    }
}
