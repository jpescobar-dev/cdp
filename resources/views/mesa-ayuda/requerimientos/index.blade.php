@extends('layouts.theme.app')

@section('title', 'Requerimientos Mesa de Ayuda')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0">Requerimientos Mesa de Ayuda</h1>
            <small class="text-muted">Bandeja interna de revisión y clasificación.</small>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" class="card card-body mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label">Folio</label>
                <input type="text" name="folio" value="{{ request('folio') }}" class="form-control" placeholder="Ej: 7950738">
            </div>
            <div class="col-md-3">
                <label class="form-label">Clasificación</label>
                <select name="clasificacion" class="form-control">
                    <option value="">Todas</option>
                    <option value="certificado_disponibilidad_presupuestaria" @selected(request('clasificacion') === 'certificado_disponibilidad_presupuestaria')>CDP</option>
                    <option value="posible_certificado_disponibilidad_presupuestaria" @selected(request('clasificacion') === 'posible_certificado_disponibilidad_presupuestaria')>Posible CDP</option>
                    <option value="otro" @selected(request('clasificacion') === 'otro')>Otro</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Requiere CDP</label>
                <select name="requiere_cdp" class="form-control">
                    <option value="">Todos</option>
                    <option value="1" @selected(request('requiere_cdp') === '1')>Sí</option>
                    <option value="0" @selected(request('requiere_cdp') === '0')>No</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Estado interno</label>
                <select name="estado" class="form-control">
                    <option value="" @selected(request('estado', '') === '')>Activos (sin terminar)</option>
                    <option value="todos" @selected(request('estado') === 'todos')>Todos</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado->id }}" @selected(request('estado') == $estado->id)>
                            {{ ucwords(str_replace('_', ' ', strtolower($estado->nombre))) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" type="submit">Filtrar</button>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Fecha</th>
                        <th>Estado externo</th>
                        <th>Tipo</th>
                        <th>Tribunal</th>
                        <th>Clasificación</th>
                        <th>Estado</th>
                        <th>Adjuntos</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requerimientos as $requerimiento)
                        <tr>
                            <td><strong>{{ $requerimiento->folio }}</strong></td>
                            <td>{{ optional($requerimiento->fecha_hora)->format('d-m-Y H:i') ?? '—' }}</td>
                            <td>{{ $requerimiento->estado_externo ?? '—' }}</td>
                            <td>{{ $requerimiento->tipo_requerimiento ?? $requerimiento->componente ?? '—' }}</td>
                            <td>{{ $requerimiento->tribunal ?? '—' }}</td>
                            <td>
                                @if($requerimiento->requiere_cdp)
                                    <span class="badge bg-success">CDP</span>
                                @elseif($requerimiento->clasificacion)
                                    <span class="badge bg-secondary">{{ $requerimiento->clasificacion }}</span>
                                @else
                                    <span class="badge bg-light text-dark">Sin clasificar</span>
                                @endif
                            </td>
                            <td>
                                @if($requerimiento->estado)
                                    @php
                                        $claseEstado = match($requerimiento->estado->nombre) {
                                            'APROBADO_USUARIO', 'RESPONDIDO' => 'bg-success',
                                            'ERROR', 'OBSERVADO_USUARIO' => 'bg-danger',
                                            'CDP_BORRADOR_GENERADO', 'EN_REVISION_USUARIO' => 'bg-primary',
                                            'POSIBLE_CDP', 'CDP_REQUIERE_DATOS' => 'bg-warning text-dark',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $claseEstado }}">
                                        {{ ucwords(str_replace('_', ' ', strtolower($requerimiento->estado->nombre))) }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border">Sin estado</span>
                                @endif
                            </td>
                            <td>{{ $requerimiento->adjuntos->count() }}</td>
                            <td class="text-end">
                                <a href="{{ route('mesa-ayuda.requerimientos.show', $requerimiento) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No hay requerimientos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $requerimientos->links() }}
    </div>
</div>
@endsection
