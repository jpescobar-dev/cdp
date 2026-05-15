<?php

namespace App\Console\Commands;

use App\Models\MesaAyudaRequerimiento;
use App\Services\MesaAyuda\ClasificarRequerimientoMesaAyudaService;
use Illuminate\Console\Command;

class ClasificarRequerimientosMesaAyudaCommand extends Command
{
    protected $signature = 'mesa-ayuda:clasificar
        {--folio= : Clasifica un folio específico}
        {--id= : Clasifica por ID interno de mesa_ayuda_requerimientos}
        {--pendientes : Clasifica solo registros sin clasificación}
        {--all : Reclasifica todos los registros}
        {--limit= : Límite de registros a procesar}';

    protected $description = 'Clasifica requerimientos importados desde Mesa de Ayuda, identificando CDP, posibles CDP u otros.';

    public function handle(ClasificarRequerimientoMesaAyudaService $service): int
    {
        $query = MesaAyudaRequerimiento::query()->orderBy('created_at');

        if ($id = $this->option('id')) {
            $query->where('id', $id);
        } elseif ($folio = $this->option('folio')) {
            $query->where('folio', $folio);
        } elseif ($this->option('pendientes')) {
            $query->where(function ($q) {
                $q->whereNull('clasificacion')->orWhere('clasificacion', '');
            });
        } elseif (!$this->option('all')) {
            $this->warn('Indica --folio=, --id=, --pendientes o --all. No se ejecutó clasificación.');
            return self::INVALID;
        }

        if ($limit = $this->option('limit')) {
            $query->limit((int) $limit);
        }

        $total = 0;
        $errores = 0;

        foreach ($query->get() as $requerimiento) {
            try {
                $resultado = $service->clasificar($requerimiento);
                $total++;

                $this->line(sprintf(
                    'Folio %s => %s | confianza=%s | score=%s | destino=%s',
                    $requerimiento->folio,
                    $resultado['clasificacion'],
                    $resultado['confianza_clasificacion'],
                    $resultado['score_clasificacion'],
                    $resultado['destino_flujo']
                ));
            } catch (\Throwable $e) {
                $errores++;
                $this->error(sprintf('Error clasificando folio %s: %s', $requerimiento->folio, $e->getMessage()));
            }
        }

        $this->info("Clasificación finalizada. Procesados: {$total}. Errores: {$errores}.");

        return $errores > 0 ? self::FAILURE : self::SUCCESS;
    }
}
