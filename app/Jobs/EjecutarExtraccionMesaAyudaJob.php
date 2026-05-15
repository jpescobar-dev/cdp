<?php

namespace App\Jobs;

use App\Services\MesaAyuda\EjecutarExtraccionMesaAyudaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EjecutarExtraccionMesaAyudaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 900;

    public function __construct(
        public ?string $ejecutadoPorRut = null,
        public array $opciones = []
    ) {}

    public function handle(EjecutarExtraccionMesaAyudaService $service): void
    {
        $service->ejecutar($this->ejecutadoPorRut, $this->opciones);
    }
}
