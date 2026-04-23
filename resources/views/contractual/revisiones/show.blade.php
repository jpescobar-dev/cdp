@extends('layouts.theme.app')

@section('title', 'Detalle Revisión Contractual')
@section('title2', 'Detalle')

@section('content')
<div class="widget-content widget-content-area br-6 mt-2 mb-2">

    <div class="row mb-4">
        <div class="col-md-4 d-flex align-items-center"></div>

        <div class="col-md-4 text-center">
            <h4 class="mb-0">Detalle Revisión Contractual</h4>
        </div>

        <div class="col-md-4 text-right">
            <a href="{{ route('contractual.revisiones.edit', $revision) }}"
               class="btn btn-outline-warning btn-sm"
               title="Editar revisión">
                <svg xmlns="http://www.w3.org/2000/svg"
                     width="22"
                     height="22"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.5"
                     stroke-linecap="round"
                     stroke-linejoin="round"
                     class="feather feather-edit-2">
                    <path d="M17 3a2.828 2.828 0 1 1 4 4L7 21l-4 1 1-4L17 3z"></path>
                </svg>
            </a>

            <a href="{{ route('contractual.revisiones.index') }}"
               class="btn btn-outline-secondary btn-sm"
               title="Volver al listado">
                <svg xmlns="http://www.w3.org/2000/svg"
                     width="22"
                     height="22"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.5"
                     stroke-linecap="round"
                     stroke-linejoin="round"
                     class="feather feather-arrow-left-circle">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 8 8 12 12 16"></polyline>
                    <line x1="16" y1="12" x2="8" y2="12"></line>
                </svg>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-3" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger mb-3" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-xl-12 mb-4">
            <div class="widget widget-table-one">
                <div class="widget-heading">
                    <h5 class="">Información General</h5>
                </div>
                <div class="widget-content">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <tbody>
                                <tr>
                                    <th width="20%">ID</th>
                                    <td>{{ $revision->id }}</td>
                                    <th width="20%">Estado</th>
                                    <td>{{ $revision->estado->nombre ?? 'SIN ESTADO' }}</td>
                                </tr>
                                <tr>
                                    <th>Título</th>
                                    <td colspan="3">{{ $revision->titulo }}</td>
                                </tr>
                                <tr>
                                    <th>Descripción</th>
                                    <td colspan="3">{{ $revision->descripcion ?: 'Sin descripción' }}</td>
                                </tr>
                                <tr>
                                    <th>Usuario creador</th>
                                    <td>{{ $revision->usuario->name ?? 'N/D' }}</td>
                                    <th>Fecha creación</th>
                                    <td>{{ $revision->created_at ? $revision->created_at->format('d-m-Y H:i') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Última actualización</th>
                                    <td>{{ $revision->updated_at ? $revision->updated_at->format('d-m-Y H:i') : '-' }}</td>
                                    <th>Total documentos</th>
                                    <td>{{ $revision->documentos->count() }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-12 mb-4">
            <div class="widget widget-table-one">
                <div class="widget-heading d-flex justify-content-between align-items-center">
                    <h5 class="">Cargar Documento</h5>
                </div>
                <div class="widget-content">
                    <form action="{{ route('contractual.revisiones.documentos.store', $revision) }}"
                          method="POST"
                          enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="archivo" class="form-label">Archivo</label>
                                <input type="file"
                                       name="archivo"
                                       id="archivo"
                                       class="form-control @error('archivo') is-invalid @enderror"
                                       required>
                                @error('archivo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tipo_documento" class="form-label">Tipo de documento</label>
                                <input type="text"
                                       name="tipo_documento"
                                       id="tipo_documento"
                                       class="form-control @error('tipo_documento') is-invalid @enderror"
                                       value="{{ old('tipo_documento') }}"
                                       placeholder="Ej.: Contrato, Bases, Resolución, Garantía">
                                @error('tipo_documento')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary btn-sm">
                                Subir Documento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
<div class="col-xl-12 mb-4">
    <div class="widget widget-table-one">
        <div class="widget-heading d-flex justify-content-between align-items-center">
            <h5 class="">Snapshots de la Revisión</h5>

            <div class="d-flex gap-2">

            {{-- BOTÓN ANALIZAR IA --}}
            <form action="{{ route('contractual.revisiones.analizar', $revision) }}"
                method="POST"
                class="d-inline">
                @csrf
                <button type="submit"
                        class="btn btn-outline-success btn-sm"
                        onclick="return confirm('¿Deseas ejecutar el análisis con IA?');">
                    Analizar con IA
                </button>
            </form>

            {{-- SNAPSHOT --}}
            <form action="{{ route('contractual.revisiones.snapshots.store', $revision) }}"
                method="POST"
                class="d-inline">
                @csrf
                <button type="submit"
                        class="btn btn-outline-primary btn-sm"
                        onclick="return confirm('¿Deseas generar un nuevo snapshot de esta revisión?');">
                    Guardar Snapshot
                </button>
            </form>

            <a href="{{ route('contractual.revisiones.snapshots.index', $revision) }}"
            class="btn btn-outline-secondary btn-sm">
                Ver historial
            </a>

        </div>
        </div>

        <div class="widget-content">
            @if($revision->snapshots->count())
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Versión</th>
                                <th>Tipo</th>
                                <th>Actual</th>
                                <th>Usuario</th>
                                <th>Fecha</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($revision->snapshots->take(5) as $snapshot)
                                <tr>
                                    <td>{{ $snapshot->id }}</td>
                                    <td>{{ $snapshot->numero_version }}</td>
                                    <td>{{ strtoupper($snapshot->tipo_ejecucion) }}</td>
                                    <td>
                                        @if($snapshot->es_actual)
                                            <span class="badge badge-success">Sí</span>
                                        @else
                                            <span class="badge badge-light">No</span>
                                        @endif
                                    </td>
                                    <td>{{ $snapshot->usuario->name ?? 'N/D' }}</td>
                                    <td>{{ $snapshot->created_at ? $snapshot->created_at->format('d-m-Y H:i') : '-' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('contractual.revisiones.snapshots.show', [$revision, $snapshot]) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Ver snapshot">
                                            Ver
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($revision->snapshots->count() > 5)
                    <div class="mt-3 text-right">
                        <small class="text-muted">Se muestran los últimos 5 snapshots.</small>
                    </div>
                @endif
            @else
                <div class="alert alert-light border mb-0">
                    No existen snapshots para esta revisión.
                </div>
            @endif
        </div>
    </div>
</div>
        <div class="col-xl-12">
            <div class="widget widget-table-one">
                <div class="widget-heading d-flex justify-content-between align-items-center">
                    <h5 class="">Documentos Asociados</h5>
                    <a href="{{ route('contractual.revisiones.documentos.index', $revision) }}"
                       class="btn btn-outline-primary btn-sm">
                        Ver todos
                    </a>
                </div>

                <div class="widget-content">
                    @if($revision->documentos->count())
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre original</th>
                                        <th>Tipo</th>
                                        <th>Tamaño</th>
                                        <th>Usuario</th>
                                        <th>Fecha</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($revision->documentos->take(10) as $documento)
                                        <tr>
                                            <td>{{ $documento->id }}</td>
                                            <td>{{ $documento->nombre_original }}</td>
                                            <td>{{ $documento->tipo_documento ?: '-' }}</td>
                                            <td>
                                                @if($documento->tamano)
                                                    {{ number_format($documento->tamano / 1024, 2) }} KB
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $documento->usuario->name ?? 'N/D' }}</td>
                                            <td>{{ $documento->created_at ? $documento->created_at->format('d-m-Y H:i') : '-' }}</td>
                                            <td class="text-center">
                                                <a href="{{ asset('storage/' . $documento->ruta) }}"
                                                   target="_blank"
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="Ver">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                         width="16"
                                                         height="16"
                                                         viewBox="0 0 24 24"
                                                         fill="none"
                                                         stroke="currentColor"
                                                         stroke-width="2"
                                                         stroke-linecap="round"
                                                         stroke-linejoin="round"
                                                         class="feather feather-eye">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                        <circle cx="12" cy="12" r="3"></circle>
                                                    </svg>
                                                </a>

                                                <form action="{{ route('contractual.revisiones.documentos.destroy', [$revision, $documento]) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('¿Está seguro de eliminar este documento?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-danger"
                                                            title="Eliminar">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                             width="16"
                                                             height="16"
                                                             viewBox="0 0 24 24"
                                                             fill="none"
                                                             stroke="currentColor"
                                                             stroke-width="2"
                                                             stroke-linecap="round"
                                                             stroke-linejoin="round"
                                                             class="feather feather-trash-2">
                                                            <polyline points="3 6 5 6 21 6"></polyline>
                                                            <path d="M19 6l-1 14H6L5 6"></path>
                                                            <path d="M10 11v6"></path>
                                                            <path d="M14 11v6"></path>
                                                            <path d="M9 6V4h6v2"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($revision->documentos->count() > 10)
                            <div class="mt-3 text-right">
                                <small class="text-muted">
                                    Se muestran los últimos 10 documentos cargados.
                                </small>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-light border mb-0">
                            No existen documentos cargados para esta revisión.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>    
</div>
@endsection