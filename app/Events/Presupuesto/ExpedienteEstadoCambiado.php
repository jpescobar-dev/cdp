<?php

namespace App\Events\Presupuesto;

use App\Models\Estado;
use App\Models\Funcionario;
use App\Models\Presupuesto\ExpedientePresupuestario;
use App\Models\Presupuesto\ExpedienteTarea;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExpedienteEstadoCambiado
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public ExpedientePresupuestario $expediente,
        public Estado $estadoOrigen,
        public Estado $estadoDestino,
        public Funcionario $usuarioEjecutor,
        public ?string $comentario = null,
        public ?ExpedienteTarea $tarea = null,
    ) {
    }
}
