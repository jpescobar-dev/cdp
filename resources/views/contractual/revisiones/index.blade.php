@extends('layouts.theme.app')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/table/datatable/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/table/datatable/dt-global_style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/table/datatable/custom_dt_html5.css') }}">
    <link href="{{ asset('assets/css/scrollspyNav.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/forms/theme-checkbox-radio.css') }}">
    <link href="{{ asset('assets/css/tables/table-basic.css') }}" rel="stylesheet" type="text/css" />

    <style>
        table.table-hover tbody tr:hover td {
            color: #46576f;
            font-weight: 500;
        }

        .table .btn.btn-sm {
            padding: 0.30rem 0.55rem;
            line-height: 1;
        }

        .table .btn svg {
            vertical-align: middle;
        }

        .table .action-buttons {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
        }
    </style>
@endsection

@section('title', 'Revisiones Contractuales')
@section('title2', 'Índice')

@section('content')
<div class="widget-content widget-content-area br-6 mt-2 mb-2">
    <div class="row mb-4">
        <div class="col-md-4 d-flex align-items-center"></div>

        <div class="col-md-4 text-center">
            <h4 class="mb-0">Listado Revisiones Contractuales</h4>
        </div>

        <div class="col-md-4 text-right">
            <a href="{{ route('contractual.revisiones.create') }}"
               class="btn btn-outline-primary btn-sm"
               title="Nueva Revisión Contractual">
                <svg xmlns="http://www.w3.org/2000/svg"
                     width="22"
                     height="22"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.5"
                     stroke-linecap="round"
                     stroke-linejoin="round"
                     class="feather feather-plus-circle">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="16"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-3" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger mb-3" role="alert">
            {{ session('error') }}
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
                                <th>Título</th>
                                <th>Estado</th>
                                <th>Usuario</th>
                                <th>Fecha creación</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($revisiones as $revision)
                                <tr>
                                    <td>{{ $revision->id }}</td>
                                    <td>{{ $revision->titulo }}</td>
                                    <td>{{ $revision->estado->nombre ?? 'SIN ESTADO' }}</td>
                                    <td>{{ $revision->usuario->name ?? 'N/D' }}</td>
                                    <td>{{ $revision->created_at ? $revision->created_at->format('d-m-Y H:i') : '-' }}</td>
                                    <td class="text-center">
                                        <div class="action-buttons">
                                            <a href="{{ route('contractual.revisiones.show', $revision) }}"
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

                                            <a href="{{ route('contractual.revisiones.edit', $revision) }}"
                                               class="btn btn-sm btn-outline-warning"
                                               title="Editar">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     width="16"
                                                     height="16"
                                                     viewBox="0 0 24 24"
                                                     fill="none"
                                                     stroke="currentColor"
                                                     stroke-width="2"
                                                     stroke-linecap="round"
                                                     stroke-linejoin="round"
                                                     class="feather feather-edit-2">
                                                    <path d="M17 3a2.828 2.828 0 1 1 4 4L7 21l-4 1 1-4L17 3z"></path>
                                                </svg>
                                            </a>

                                            <form action="{{ route('contractual.revisiones.destroy', $revision) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('¿Está seguro de eliminar esta Revisión Contractual?');">
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
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No existen revisiones contractuales registradas.</td>
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