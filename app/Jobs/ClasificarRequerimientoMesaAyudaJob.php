<?php

namespace App\Jobs;

use App\Models\MesaAyudaRequerimiento;
use App\Services\MesaAyuda\ClasificarRequerimientoMesaAyudaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClasificarRequerimientoMesaAyudaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $mesaAyudaRequerimientoId)
    {
    }

    public function handle(ClasificarRequerimientoMesaAyudaService $service): void
    {
        $requerimiento = MesaAyudaRequerimiento::query()->findOrFail($this->mesaAyudaRequerimientoId);
        $service->clasificar($requerimiento);
    }
}
