@extends('layouts.theme.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">{{ $expediente->correlativo }}</h1>
        <a href="{{ route('presupuesto.expedientes.index') }}" class="btn btn-outline-secondary">Volver</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header">Datos del expediente</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Estado</dt>
                        <dd class="col-sm-8"><span class="badge bg-secondary">{{ $expediente->estado->nombre ?? 'Sin estado' }}</span></dd>

                        <dt class="col-sm-4">Solicitante</dt>
                        <dd class="col-sm-8">{{ $expediente->solicitante->nombre_completo ?? $expediente->solicitante_rut }}</dd>

                        <dt class="col-sm-4">Responsable</dt>
                        <dd class="col-sm-8">{{ $expediente->responsable->nombre_completo ?? 'Sin asignar' }}</dd>

                        <dt class="col-sm-4">Cuenta</dt>
                        <dd class="col-sm-8">{{ $expediente->cuenta_presupuestaria }} {{ $expediente->denominacion ? '- '.$expediente->denominacion : '' }}</dd>

                        <dt class="col-sm-4">Monto</dt>
                        <dd class="col-sm-8">${{ number_format($expediente->monto, 0, ',', '.') }} {{ $expediente->moneda }}</dd>

                        <dt class="col-sm-4">Glosa</dt>
                        <dd class="col-sm-8">{{ $expediente->glosa }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Historial</div>
                <div class="card-body table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Usuario</th>
                                <th>Comentario</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expediente->historial as $hito)
                                <tr>
                                    <td>{{ optional($hito->fecha_cambio)->format('d-m-Y H:i') }}</td>
                                    <td>{{ $hito->estado->nombre ?? '' }}</td>
                                    <td>{{ $hito->usuario->nombre_completo ?? $hito->usuario_rut }}</td>
                                    <td>{{ $hito->comentario }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted text-center">Sin historial.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">Cambio de estado</div>
                <div class="card-body">
                    @php
                        $estadoActual = $expediente->estado->nombre ?? null;
                        $siguienteEstado = match ($estadoActual) {
                            'Ingresado' => 'En revisión',
                            'En revisión' => 'Aprobado',
                            'Aprobado' => 'Emitido',
                            default => null,
                        };
                        $estadoDestino = $siguienteEstado
                            ? \App\Models\Estado::where('nombre', $siguienteEstado)
                                ->where('tabla_referencia', 'expedientes_presupuestarios')
                                ->first()
                            : null;
                    @endphp

                    @if($estadoDestino)
                        <form method="POST" action="{{ route('presupuesto.expedientes.cambiar-estado', $expediente) }}">
                            @csrf
                            <input type="hidden" name="estado_destino_id" value="{{ $estadoDestino->id }}">
                            <div class="mb-3">
                                <label class="form-label">Comentario</label>
                                <textarea name="comentario" class="form-control" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                Pasar a {{ $estadoDestino->nombre }}
                            </button>
                        </form>
                    @else
                        <div class="alert alert-success mb-0">Proceso finalizado.</div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">Tareas</div>
                <div class="card-body">
                    @forelse($expediente->tareas as $tarea)
                        <div class="border rounded p-2 mb-2">
                            <strong>{{ $tarea->titulo }}</strong><br>
                            <small class="text-muted">{{ $tarea->estado }}</small>
                        </div>
                    @empty
                        <span class="text-muted">Sin tareas.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
