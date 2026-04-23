@extends('layouts.theme.app')

@section('title', 'Detalle Hallazgo')
@section('title2', 'Detalle')

@section('content')
<div class="widget-content widget-content-area br-6 mt-2 mb-2">
    <div class="row mb-4">
        <div class="col-md-8">
            <h4 class="mb-0">Detalle Hallazgo</h4>
            <small class="text-muted">Snapshot v{{ $snapshot->numero_version }} | {{ $revision->titulo }}</small>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('contractual.revisiones.snapshots.hallazgos.index', [$revision, $snapshot]) }}"
               class="btn btn-outline-secondary btn-sm">Volver</a>
        </div>
    </div>

    <div class="widget widget-table-one">
        <div class="widget-content">
            <table class="table table-bordered mb-0">
                <tbody>
                    <tr>
                        <th width="20%">ID</th>
                        <td>{{ $hallazgo->id }}</td>
                        <th width="20%">Estado</th>
                        <td>{{ $hallazgo->estado->nombre ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Título</th>
                        <td colspan="3">{{ $hallazgo->titulo }}</td>
                    </tr>
                    <tr>
                        <th>Tipo hallazgo</th>
                        <td>{{ $hallazgo->tipo_hallazgo ?: '-' }}</td>
                        <th>Criticidad</th>
                        <td>{{ $hallazgo->nivel_criticidad ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tipo riesgo</th>
                        <td colspan="3">{{ $hallazgo->tipo_riesgo ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Hecho acreditado</th>
                        <td colspan="3">{{ $hallazgo->hecho_acreditado ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Observación</th>
                        <td colspan="3">{{ $hallazgo->observacion ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Fundamento documental</th>
                        <td colspan="3">{{ $hallazgo->fundamento_documental ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Consecuencia posible</th>
                        <td colspan="3">{{ $hallazgo->consecuencia_posible ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Recomendación</th>
                        <td colspan="3">{{ $hallazgo->recomendacion ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Usuario</th>
                        <td>{{ $hallazgo->usuario->name ?? 'N/D' }}</td>
                        <th>Fecha</th>
                        <td>{{ $hallazgo->created_at ? $hallazgo->created_at->format('d-m-Y H:i') : '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
