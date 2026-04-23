@extends('layouts.theme.app')

@section('title', 'Detalle Snapshot')
@section('title2', 'Detalle')

@section('content')
<div class="widget-content widget-content-area br-6 mt-2 mb-2">
    <div class="row mb-4">
        <div class="col-md-4 d-flex align-items-center"></div>

        <div class="col-md-4 text-center">
            <h4 class="mb-0">Snapshot v{{ $snapshot->numero_version }}</h4>
        </div>

        <div class="col-md-4 text-right">
            <a href="{{ route('contractual.revisiones.snapshots.index', $revision) }}"
               class="btn btn-outline-secondary btn-sm">
                Volver
            </a>
        </div>
    </div>

    <div class="widget widget-table-one">
        <div class="widget-content">
            <table class="table table-bordered mb-0">
                <tbody>
                    <tr>
                        <th width="20%">ID</th>
                        <td>{{ $snapshot->id }}</td>
                        <th width="20%">Versión</th>
                        <td>{{ $snapshot->numero_version }}</td>
                    </tr>
                    <tr>
                        <th>Tipo de ejecución</th>
                        <td>{{ strtoupper($snapshot->tipo_ejecucion) }}</td>
                        <th>Actual</th>
                        <td>{{ $snapshot->es_actual ? 'Sí' : 'No' }}</td>
                    </tr>
                    <tr>
                        <th>Usuario</th>
                        <td>{{ $snapshot->usuario->name ?? 'N/D' }}</td>
                        <th>Fecha</th>
                        <td>{{ $snapshot->created_at ? $snapshot->created_at->format('d-m-Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Resumen</th>
                        <td colspan="3">{{ $snapshot->resumen ?: 'Sin resumen' }}</td>
                    </tr>
                </tbody>
            </table>

            @if(!empty($snapshot->json_resultado))
                <div class="mt-4">
                    <h5>Resultado JSON</h5>
                    <pre class="bg-light p-3 border rounded">{{ json_encode($snapshot->json_resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection