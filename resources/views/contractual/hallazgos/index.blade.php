@extends('layouts.theme.app')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/table/datatable/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/table/datatable/dt-global_style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/table/datatable/custom_dt_html5.css') }}">
    <link href="{{ asset('assets/css/scrollspyNav.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/forms/theme-checkbox-radio.css') }}">
    <link href="{{ asset('assets/css/tables/table-basic.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Hallazgos')
@section('title2', 'Índice')

@section('content')
<div class="widget-content widget-content-area br-6 mt-2 mb-2">
    <div class="row mb-4">
        <div class="col-md-4 d-flex align-items-center"></div>
        <div class="col-md-4 text-center">
            <h4 class="mb-0">Hallazgos Snapshot v{{ $snapshot->numero_version }}</h4>
            <small class="text-muted">{{ $revision->titulo }}</small>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('contractual.revisiones.snapshots.hallazgos.create', [$revision, $snapshot]) }}"
               class="btn btn-outline-primary btn-sm">Nuevo</a>
            <a href="{{ route('contractual.revisiones.snapshots.show', [$revision, $snapshot]) }}"
               class="btn btn-outline-secondary btn-sm">Volver</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-3" role="alert">{{ session('success') }}</div>
    @endif

    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="widget widget-table-one">
                <div class="table-responsive">
                    <table id="html5-extension" class="table table-hover table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Tipo</th>
                                <th>Riesgo</th>
                                <th>Criticidad</th>
                                <th>Estado</th>
                                <th>Usuario</th>
                                <th>Fecha creación</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($snapshot->hallazgos as $hallazgo)
                                <tr>
                                    <td>{{ $hallazgo->id }}</td>
                                    <td>{{ $hallazgo->titulo }}</td>
                                    <td>{{ $hallazgo->tipo_hallazgo ?: '-' }}</td>
                                    <td>{{ $hallazgo->tipo_riesgo ?: '-' }}</td>
                                    <td>{{ $hallazgo->nivel_criticidad ?: '-' }}</td>
                                    <td>{{ $hallazgo->estado->nombre ?? '-' }}</td>
                                    <td>{{ $hallazgo->usuario->name ?? 'N/D' }}</td>
                                    <td>{{ $hallazgo->created_at ? $hallazgo->created_at->format('d-m-Y H:i') : '-' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('contractual.revisiones.snapshots.hallazgos.show', [$revision, $snapshot, $hallazgo]) }}"
                                           class="btn btn-sm btn-outline-primary">Ver</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">No existen hallazgos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
