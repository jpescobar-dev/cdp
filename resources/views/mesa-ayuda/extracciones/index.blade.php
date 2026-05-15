@extends('layouts.theme.app')

@section('title', 'Mesa de Ayuda - Extracciones')

@section('content')
<div class="row layout-top-spacing">
    <div class="col-12">
        <div class="widget-content widget-content-area br-6">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-1">Extracciones Mesa de Ayuda</h4>
                    <p class="mb-0 text-muted">Conexión segura en modo solo lectura para generar JSON, importar y clasificar requerimientos.</p>
                </div>

                <div>
                    @include('mesa-ayuda.partials.boton-conectar')
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="alert alert-info mb-3">
                <strong>Modo actual:</strong>
                Solo lectura: <strong>{{ config('mesa_ayuda.solo_lectura') ? 'Sí' : 'No' }}</strong> |
                Respuesta automática: <strong>{{ config('mesa_ayuda.permitir_respuesta') ? 'Activada' : 'Desactivada' }}</strong> |
                Headless: <strong>{{ config('mesa_ayuda.headless') ? 'true' : 'false' }}</strong>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-hover table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Inicio</th>
                            <th>Término</th>
                            <th>Estado</th>
                            <th>Detectados</th>
                            <th>Importados</th>
                            <th>Errores</th>
                            <th>JSON</th>
                            <th>Error</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($extracciones as $extraccion)
                            <tr>
                                <td>{{ $extraccion->id }}</td>
                                <td>{{ optional($extraccion->fecha_inicio)->format('d-m-Y H:i:s') }}</td>
                                <td>{{ optional($extraccion->fecha_termino)->format('d-m-Y H:i:s') }}</td>
                                <td>
                                    <span class="badge badge-{{ $extraccion->estado === 'completado' ? 'success' : ($extraccion->estado === 'error' ? 'danger' : 'secondary') }}">
                                        {{ $extraccion->estado }}
                                    </span>
                                </td>
                                <td>{{ $extraccion->total_detectados }}</td>
                                <td>{{ $extraccion->total_importados }}</td>
                                <td>{{ $extraccion->total_errores }}</td>
                                <td class="small">{{ $extraccion->ruta_json }}</td>
                                <td class="small text-danger">{{ Str::limit($extraccion->mensaje_error, 120) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    Aún no existen extracciones registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
