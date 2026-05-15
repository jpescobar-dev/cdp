# Actualización de menú - Mesa de Ayuda / CDP / Agentes

## Archivo incluido

Copiar en el proyecto:

```text
resources/views/layouts/theme/partials/topnavbar.blade.php
```

## Funcionalidades agregadas al menú

### Presupuesto
- Expedientes
- Mis tareas
- Borradores CDP
- CDP emitidos

### Mesa de Ayuda
- Requerimientos
- Ingreso manual
- Extracciones
- Ejecutar extractor

### Agentes
- Ejecuciones
- Monitor

### Sistema
- Usuarios

## Permisos esperados

```text
ver dashboard
ver expedientes presupuestarios
ver tareas presupuestarias
ver cdp borradores
ver cdp
ver mesa ayuda
ver requerimientos mesa ayuda
crear requerimientos mesa ayuda
ejecutar extractor mesa ayuda
ver extracciones mesa ayuda
ver agentes
ver ejecuciones agentes
reintentar ejecuciones agentes
ver usuarios
```

## Rutas esperadas

El menú usa `Route::has()` antes de generar cada URL. Si una ruta aún no existe, el enlace queda en `#` y no rompe la vista.

Rutas consideradas:

```text
dashboard
presupuesto.expedientes.index
presupuesto.tareas.index
cdp-borradores.index
cdps.index
mesa-ayuda.requerimientos.index
mesa-ayuda.requerimientos.create
mesa-ayuda.extracciones.index
mesa-ayuda.extractor.index
agente-ejecuciones.index
agentes.monitor
usuarios.index
```

## Corrección incluida

Se corrigió el enlace incompleto de `Presupuesto > Expedientes`, que estaba comentado parcialmente y podía dejar HTML inválido.

## Nota Bootstrap

Se incluyeron ambos atributos `data-toggle="collapse"` y `data-bs-toggle="collapse"` para compatibilidad con Bootstrap 4 y 5. El layout actual carga Bootstrap desde `bootstrap/js/bootstrap.min.js`, por lo que `data-toggle` es necesario.
