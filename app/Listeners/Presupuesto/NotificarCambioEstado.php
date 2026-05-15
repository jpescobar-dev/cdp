<?php

namespace App\Listeners\Presupuesto;

use App\Events\Presupuesto\ExpedienteEstadoCambiado;

class NotificarCambioEstado
{
    public function handle(ExpedienteEstadoCambiado $event): void
    {
        // Punto de enganche para notificación interna/correo.
        // Ejemplo futuro: Notification::send($usuario, new ExpedienteAsignadoNotification($event->expediente));
    }
}
