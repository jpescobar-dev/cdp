<?php

namespace App\Listeners\Presupuesto;

use App\Events\Presupuesto\ExpedienteEstadoCambiado;
use Illuminate\Support\Facades\Log;

class NotificarCambioEstado
{
    public function handle(ExpedienteEstadoCambiado $event): void
    {
        // Placeholder inicial.
        // Aquí puedes conectar notificaciones internas, correo o cualquier canal adicional.
        Log::info('Cambio de estado de expediente', [
            'expediente_id' => $event->expediente->id,
            'correlativo' => $event->expediente->correlativo,
            'estado_origen' => $event->estadoOrigen->nombre,
            'estado_destino' => $event->estadoDestino->nombre,
            'usuario_ejecutor' => $event->usuarioEjecutor->rut,
        ]);
    }
}
