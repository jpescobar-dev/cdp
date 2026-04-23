<?php

namespace App\Listeners\Presupuesto;

use App\Events\Presupuesto\ExpedienteEstadoCambiado;

class CrearTareaPorCambioEstado
{
    public function handle(ExpedienteEstadoCambiado $event): void
    {
        // La tarea ya se crea dentro del servicio para mantener atomicidad en la transacción.
        // Este listener queda disponible si luego quieres registrar métricas o disparar procesos externos.
    }
}
