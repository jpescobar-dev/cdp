@extends('layouts.theme.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Solicitud CDP #{{ $solicitud->id }}</h1>
        <a href="{{ route('cdp.solicitudes.index') }}" class="btn btn-outline-secondary">Volver</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Alerta de acción requerida --}}
    @if($solicitud->estado === 'pdf_generado')
        <div class="alert alert-info d-flex align-items-center gap-2">
            <i data-feather="info" style="width:20px;height:20px;flex-shrink:0;"></i>
            <div>
                <strong>Siguiente paso:</strong> Descargue el PDF y adjúntelo manualmente a su requerimiento en
                <strong>Mesa de Ayuda</strong>.
            </div>
        </div>
    @endif

    <div class="row g-4">
        {{-- Datos del requirente --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header fw-semibold">Datos del requirente</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5 text-muted">Nombre</dt>
                        <dd class="col-sm-7">{{ $solicitud->nombre_requirente }}</dd>

                        <dt class="col-sm-5 text-muted">RUT</dt>
                        <dd class="col-sm-7">{{ $solicitud->rut_requirente }}</dd>

                        <dt class="col-sm-5 text-muted">Unidad</dt>
                        <dd class="col-sm-7">{{ $solicitud->unidad_requirente }}</dd>

                        @if($solicitud->ccosto)
                            <dt class="col-sm-5 text-muted">Centro de costo</dt>
                            <dd class="col-sm-7">{{ $solicitud->ccosto }}</dd>
                        @endif

                        @if($solicitud->requerimiento)
                            <dt class="col-sm-5 text-muted">N° Requerimiento</dt>
                            <dd class="col-sm-7">{{ $solicitud->requerimiento }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        {{-- Datos del gasto --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header fw-semibold">Datos del gasto</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5 text-muted">Proveedor</dt>
                        <dd class="col-sm-7">{{ $solicitud->proveedor }}</dd>

                        <dt class="col-sm-5 text-muted">Monto estimado</dt>
                        <dd class="col-sm-7">{{ $solicitud->montoFormateado() }}</dd>

                        @if($solicitud->tipo_gasto1)
                            <dt class="col-sm-5 text-muted">Tipo de gasto</dt>
                            <dd class="col-sm-7">
                                {{ $solicitud->tipo_gasto1 === 'GO' ? 'GO — Gasto Operacional' : 'INI — Inversión' }}
                            </dd>
                        @endif

                        @if($solicitud->tipo_gasto2)
                            <dt class="col-sm-5 text-muted">Clasificación</dt>
                            <dd class="col-sm-7">{{ ucfirst(strtolower($solicitud->tipo_gasto2)) }}</dd>
                        @endif

                        @if($solicitud->proyecto_id)
                            <dt class="col-sm-5 text-muted">Proyecto</dt>
                            <dd class="col-sm-7">{{ $solicitud->proyecto?->codigo }} — {{ $solicitud->proyecto?->proyecto }}</dd>
                        @endif

                        <dt class="col-sm-5 text-muted">Fecha</dt>
                        <dd class="col-sm-7">{{ $solicitud->created_at->format('d/m/Y H:i') }}</dd>

                        <dt class="col-sm-5 text-muted">Estado</dt>
                        <dd class="col-sm-7">
                            @if($solicitud->estado === 'pdf_generado')
                                <span class="badge bg-success">PDF generado</span>
                            @else
                                <span class="badge bg-secondary">Borrador</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Glosa --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header fw-semibold">Descripción del gasto (glosa)</div>
                <div class="card-body">
                    <p class="mb-0" style="white-space: pre-line;">{{ $solicitud->glosa }}</p>
                </div>
            </div>
        </div>

        {{-- Documentos --}}
        @if($solicitud->documentos)
            <div class="col-12">
                <div class="card">
                    <div class="card-header fw-semibold">Documentos adjuntados</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach($solicitud->documentos as $doc)
                                <li class="list-group-item d-flex align-items-center gap-2">
                                    <i data-feather="paperclip" style="width:16px;height:16px;"></i>
                                    {{ $doc['nombre'] }}
                                    <small class="text-muted">({{ $doc['mime'] }})</small>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Descargar PDF --}}
        @if($solicitud->pdf_path)
            <div class="col-12">
                <div class="card border-primary">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-1">PDF generado y listo para descargar</h6>
                            <small class="text-muted">Adjunte este archivo a su requerimiento en Mesa de Ayuda.</small>
                        </div>
                        <a href="{{ route('cdp.solicitudes.descargar', $solicitud) }}"
                           class="btn btn-primary">
                            <i data-feather="download" style="width:16px;height:16px;" class="me-1"></i>
                            Descargar PDF
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
