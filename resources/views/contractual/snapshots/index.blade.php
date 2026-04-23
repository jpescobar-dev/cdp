@extends('layouts.theme.app')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/table/datatable/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/table/datatable/dt-global_style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/table/datatable/custom_dt_html5.css') }}">
    <link href="{{ asset('assets/css/scrollspyNav.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/forms/theme-checkbox-radio.css') }}">
    <link href="{{ asset('assets/css/tables/table-basic.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Snapshots de Revisión')
@section('title2', 'Índice')

@section('content')
<div class="widget-content widget-content-area br-6 mt-2 mb-2">
    <div class="row mb-4">
        <div class="col-md-4 d-flex align-items-center"></div>

        <div class="col-md-4 text-center">
            <h4 class="mb-0">Snapshots de la Revisión #{{ $revision->id }}</h4>
            <small class="text-muted">{{ $revision->titulo }}</small>
        </div>

        <div class="col-md-4 text-right">
            <a href="{{ route('contractual.revisiones.show', $revision) }}"
               class="btn btn-outline-secondary btn-sm"
               title="Volver">
                Volver
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-3" role="alert">
            {{ session('success') }}
            
        </div>
    @endif

    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="widget widget-table-one">
                <div class="table-responsive">
                    <table id="html5-extension" class="table table-hover table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Versión</th>
                                <th>Tipo</th>
                                <th>Resumen</th>
                                <th>Actual</th>
                                <th>Usuario</th>
                                <th>Fecha creación</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($revision->snapshots as $snapshot)
                                <tr>
                                    <td>{{ $snapshot->id }}</td>
                                    <td>{{ $snapshot->numero_version }}</td>
                                    <td>{{ strtoupper($snapshot->tipo_ejecucion) }}</td>
                                    <td>{{ $snapshot->resumen ?: '-' }}</td>
                                    <td>{{ $snapshot->es_actual ? 'Sí' : 'No' }}</td>
                                    <td>{{ $snapshot->usuario->name ?? 'N/D' }}</td>
                                    <td>{{ $snapshot->created_at ? $snapshot->created_at->format('d-m-Y H:i') : '-' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('contractual.revisiones.snapshots.show', [$revision, $snapshot]) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            Ver
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        No existen snapshots registrados.
                                    </td>
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

@section('scripts')
    <script src="{{ asset('plugins/table/datatable/datatables.js') }}"></script>
    <script src="{{ asset('plugins/table/datatable/button-ext/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('plugins/table/datatable/button-ext/jszip.min.js') }}"></script>
    <script src="{{ asset('plugins/table/datatable/button-ext/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('plugins/table/datatable/button-ext/buttons.print.min.js') }}"></script>
    <script src="{{ asset('plugins/highlight/highlight.pack.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="{{ asset('assets/js/scrollspyNav.js') }}"></script>

    <script>
        $(document).ready(function () {
            if ($.fn.DataTable.isDataTable('#html5-extension')) {
                $('#html5-extension').DataTable().destroy();
            }

            $('#html5-extension').DataTable({
                dom: `
                    <'row mb-3'
                        <'col-md-3'l>
                        <'col-md-6 text-center'B>
                        <'col-md-3'f>
                    >
                    <'row'
                        <'col-md-12'tr>
                    >
                    <'row'
                        <'col-md-5'i>
                        <'col-md-7'p>
                    >`,
                buttons: [
                    { extend: 'copy', className: 'btn' },
                    { extend: 'csv', className: 'btn' },
                    { extend: 'excel', className: 'btn' },
                    { extend: 'print', className: 'btn' }
                ],
                oLanguage: {
                    oPaginate: {
                        sPrevious: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"><path d="M15 18l-6-6 6-6"/></svg>',
                        sNext: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"><path d="M9 18l6-6-6-6"/></svg>'
                    },
                    sInfo: "Mostrando página _PAGE_ de _PAGES_",
                    sSearch: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                    sSearchPlaceholder: "Buscar...",
                    sLengthMenu: "Resultados : _MENU_",
                    sZeroRecords: "No se encontraron registros",
                    sInfoEmpty: "No hay registros disponibles",
                    sInfoFiltered: "(filtrado de _MAX_ registros totales)"
                },
                stripeClasses: [],
                lengthMenu: [10, 20, 50],
                pageLength: 10
            });
        });
    </script>
@endsection