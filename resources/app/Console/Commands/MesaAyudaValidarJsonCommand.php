<?php

namespace App\Console\Commands;

use App\Services\MesaAyuda\ValidarJsonMesaAyudaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MesaAyudaValidarJsonCommand extends Command
{
    protected $signature = 'mesa-ayuda:validar-json {ruta : Ruta relativa en storage/app o ruta absoluta del JSON}';

    protected $description = 'Valida la estructura mínima del JSON generado por el extractor de Mesa de Ayuda';

    public function handle(ValidarJsonMesaAyudaService $validator): int
    {
        $ruta = $this->argument('ruta');
        $path = str_starts_with($ruta, DIRECTORY_SEPARATOR) || preg_match('/^[A-Z]:\\\\/i', $ruta)
            ? $ruta
            : Storage::path($ruta);

        if (!file_exists($path)) {
            $this->error("No existe el archivo: {$path}");
            return self::FAILURE;
        }

        try {
            $json = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
            $validator->validar($json);
            $this->info('JSON válido. Estructura mínima OK.');
            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('JSON inválido: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
