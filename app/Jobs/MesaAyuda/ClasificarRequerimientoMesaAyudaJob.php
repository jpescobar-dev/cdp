<?php

namespace App\Jobs\MesaAyuda;

use App\Models\MesaAyudaRequerimiento;
use App\Services\MesaAyuda\OrquestadorMesaAyudaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClasificarRequerimientoMesaAyudaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 120;

    public function __construct(
        public int $mesaAyudaRequerimientoId,
        public ?int $solicitadoPorUserId = null,
    ) {}

    public function handle(OrquestadorMesaAyudaService $orquestador): void
    {
        $requerimiento = MesaAyudaRequerimiento::query()->findOrFail($this->mesaAyudaRequerimientoId);

        $orquestador->clasificarRequerimiento($requerimiento, $this->solicitadoPorUserId);
    }
}
