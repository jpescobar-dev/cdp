@extends('layouts.theme.app')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/table/datatable/datatables.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/table/datatable/dt-global_style.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/table/datatable/custom_dt_html5.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/forms/theme-checkbox-radio.css') }}">
<link href="{{ asset('assets/css/tables/table-basic.css') }}" rel="stylesheet" type="text/css" />

<style>
    table.table-hover tbody tr:hover td {
        color: #46576f;
        font-weight: 500;
    }
</style>
@endsection

@section('title', 'Usuarios')
@section('title2', 'Índice')

@section('content')

<div class="widget-content widget-content-area br-6 mt-2 mb-2">
    <div id="content" class="main-content">
        <div class="layout-px-spacing">

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4>Listado de Usuarios</h4>
                </div>

                <div>
                    @can('crear usuarios')
                        <a href="{{ route('usuarios.create') }}" class="btn btn-outline-primary btn-sm" title="Nuevo Usuario">
                            <i class="fa-solid fa-circle-plus"></i>
                        </a>
                    @endcan
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-xl-12">
                    <div class="widget widget-table-one">

                        <table id="html5-extension" class="table table-hover table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Estado</th>
                                    <th>Rol</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($usuarios as $usuario)
                                    <tr>
                                        <td>{{ $usuario->id }}</td>
                                        <td>{{ $usuario->name }}</td>
                                        <td>{{ $usuario->email }}</td>
                                        <td>
                                            @if($usuario->activo)
                                                <span class="badge badge-success">Activo</span>
                                            @else
                                                <span class="badge badge-danger">Inactivo</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($usuario->roles->count())
                                                @foreach($usuario->roles as $rol)
                                                    <span class="badge badge-info">{{ $rol->name }}</span>
                                                @endforeach
                                            @else
                                                <span class="badge badge-secondary">Sin rol</span>
                                            @endif
                                        </td>
                                        {{-- <td class="text-center">
                                            <div class="d-flex justify-content-center" style="gap: 4px;">

                                                @can('ver usuarios')
                                                    <a href="#" class="btn btn-sm btn-outline-primary" title="Ver">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                @endcan

                                                @can('editar usuarios')
                                                    <a href="#" class="btn btn-sm btn-outline-warning" title="Editar">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                @endcan

                                                @can('eliminar usuarios')
                                                    <form action="#" method="POST" class="d-inline"
                                                          onsubmit="return confirm('¿Está seguro de eliminar este usuario?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan

                                            </div>
                                        </td> --}}
                                        {{-- resources/views/usuarios/index.blade.php --}}
                    <td class="text-center">
                        <div class="d-flex justify-content-center" style="gap: 4px;">

                            @can('ver usuarios')
                                <a href="#" class="btn btn-sm btn-outline-primary" title="Ver">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            @endcan

                            @can('editar usuarios')
                                <a href="#" class="btn btn-sm btn-outline-warning" title="Editar">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            @endcan

                            @can('eliminar usuarios')
                                <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('¿Está seguro de eliminar este usuario?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            @endcan

                        </div>
                    </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No existen usuarios registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
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

<script>
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
                sPrevious: 'Anterior',
                sNext: 'Siguiente'
            },
            sInfo: "Mostrando página _PAGE_ de _PAGES_",
            sSearch: "",
            sSearchPlaceholder: "Buscar...",
            sLengthMenu: "Resultados : _MENU_"
        },
        stripeClasses: [],
        lengthMenu: [10, 20, 50],
        pageLength: 10
    });
</script>
@endsection