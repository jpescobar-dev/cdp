# Módulo base de workflow presupuestario para Laravel 10

## Incluye
- Modelos del módulo bajo `App\Models\Presupuesto`
- Controladores base de expedientes y workflow
- Request para cambio de estado
- Services del motor de workflow
- Evento y listeners base
- Seeders de estados y transiciones
- Rutas de ejemplo
- Vistas mínimas placeholder

## Ajustes obligatorios antes de usar
1. Verifica que tus migraciones del módulo ya estén ejecutadas.
2. Asegura que `users` tenga el campo `rut` o ajusta el controlador `ExpedienteWorkflowController`.
3. Registra el archivo `routes/presupuesto.php` desde `RouteServiceProvider` o inclúyelo manualmente en `routes/web.php`.
4. Registra el evento y listeners en `app/Providers/EventServiceProvider.php`.
5. Ajusta las vistas a tu layout real.
6. Si tu control de roles usa Spatie, reemplaza la validación de `rol_permitido` por chequeo real de permisos.

## EventServiceProvider sugerido
```php
protected $listen = [
    \App\Events\Presupuesto\ExpedienteEstadoCambiado::class => [
        \App\Listeners\Presupuesto\CrearTareaPorCambioEstado::class,
        \App\Listeners\Presupuesto\NotificarCambioEstado::class,
    ],
];
```

## Observación importante
El modelo asume que el flujo usa 4 estados:
- Ingresado
- En revisión
- Aprobado
- Emitido

Las observaciones no son estado. Deben manejarse en la tabla `expediente_observaciones`.
