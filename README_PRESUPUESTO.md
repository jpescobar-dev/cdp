# Módulo Presupuesto Workflow - Laravel 10

## Contenido

Incluye:

- Modelos del módulo en `app/Models/Presupuesto`
- Migraciones limpias para `estados`, `transiciones_estados` y tablas del módulo
- Controller base de expedientes
- Controller de cambio de estado
- Request de cambio de estado
- Services del motor de workflow
- Event y listener base
- Seeders de estados y transiciones
- Rutas de ejemplo en `routes/presupuesto.php`
- Vistas Blade base

## Orden importante de migraciones

Tu orden sano debe respetar dependencias:

```text
users
cfinancieros
ccostos
estados
funcionarios
transiciones_estados
expedientes_presupuestarios
expediente_historial
expediente_observaciones
expediente_tareas
expediente_adjuntos
```

## Ajustes necesarios

1. Si ya tienes una migración `estados`, reemplázala por la incluida o fusiona sus campos.
2. Elimina migraciones `alter_estados...` si aún no estás en producción.
3. Agrega las rutas en `routes/web.php`:

```php
require __DIR__.'/presupuesto.php';
```

4. En `database/seeders/DatabaseSeeder.php`, agrega:

```php
$this->call([
    EstadoPresupuestoSeeder::class,
    TransicionesEstadosSeeder::class,
]);
```

5. Registra el listener si no tienes discovery automático de eventos:

```php
protected $listen = [
    \App\Events\Presupuesto\ExpedienteEstadoCambiado::class => [
        \App\Listeners\Presupuesto\NotificarCambioEstado::class,
    ],
];
```

## Nota importante

El controller busca el funcionario usando:

```php
Funcionario::where('email', auth()->user()->email)->firstOrFail();
```

Esto evita depender de un campo `rut` en `users`, porque tu tabla `users` no lo tiene.

## Comandos sugeridos

```bash
composer dump-autoload
php artisan migrate:fresh --seed
```
