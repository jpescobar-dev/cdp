@extends('layouts.theme.app')

@section('title', 'Usuarios')
@section('title2', 'Listado')

@section('styles')
<style>
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

@section('content')
<div class="widget-content widget-content-area br-6 mt-2 mb-2">
    @if (session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
    @endif

    <div class="row mb-4">
        <div class="col-md-4"></div>
        <div class="col-md-4 text-center">
            <h4 class="mb-0">Listado de Usuarios</h4>
        </div>
        <div class="col-md-4 text-right">
            @can('usuarios.crear')
                <a href="{{ route('users.create') }}"
                   class="btn btn-outline-primary btn-sm"
                   title="Nuevo usuario">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="18"
                         height="18"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         stroke-linecap="round"
                         stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="16"></line>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                    </svg>
                </a>
            @endcan
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Activo</th>
                    <th>Cambio de clave</th>
                    <th>Roles</th>
                    @canany(['usuarios.ver', 'usuarios.editar', 'usuarios.eliminar', 'usuarios.restablecer-clave'])
                        <th class="text-center">Acciones</th>
                    @endcanany
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->is_active)
                                <span class="badge badge-success">Sí</span>
                            @else
                                <span class="badge badge-danger">No</span>
                            @endif
                        </td>
                        <td>
                            @if($user->must_change_password)
                                <span class="badge badge-warning">Pendiente</span>
                            @else
                                <span class="badge badge-primary">No</span>
                            @endif
                        </td>
                        <td>
                            @forelse($user->roles as $role)
                                <span class="badge badge-primary">{{ $role->name }}</span>
                            @empty
                                <span class="text-muted">Sin roles</span>
                            @endforelse
                        </td>

                        @canany(['usuarios.ver', 'usuarios.editar', 'usuarios.eliminar', 'usuarios.restablecer-clave'])
                            <td class="text-center">
                                <div class="action-buttons">
                                    @can('usuarios.ver')
                                        <a href="{{ route('users.show', $user) }}"
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
                                                 stroke-linejoin="round">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </a>
                                    @endcan

                                    @can('usuarios.editar')
                                        <a href="{{ route('users.edit', $user) }}"
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
                                                 stroke-linejoin="round">
                                                <path d="M17 3a2.828 2.828 0 1 1 4 4L7 21l-4 1 1-4L17 3z"></path>
                                            </svg>
                                        </a>
                                    @endcan

                                    @can('usuarios.restablecer-clave')
                                        <form action="{{ route('users.reset-password', $user) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('¿Restablecer la contraseña de este usuario?');">
                                            @csrf
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-info"
                                                    title="Restablecer contraseña">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     width="16"
                                                     height="16"
                                                     viewBox="0 0 16 16"
                                                     fill="currentColor">
                                                    <path d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 1 1 .908-.418A6 6 0 1 1 8 2v1z"/>
                                                    <path d="M8 0a.5.5 0 0 1 .5.5V3h2.5a.5.5 0 0 1 0 1H8A.5.5 0 0 1 7.5 3V.5A.5.5 0 0 1 8 0z"/>
                                                    <path d="M7 6.5A1.5 1.5 0 0 1 8.5 5h1A1.5 1.5 0 0 1 11 6.5V8h.5A1.5 1.5 0 0 1 13 9.5v2A1.5 1.5 0 0 1 11.5 13h-4A1.5 1.5 0 0 1 6 11.5v-2A1.5 1.5 0 0 1 7.5 8H8V6.5zM8 8h2V6.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5V8zm-.5 1a.5.5 0 0 0-.5.5v2a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-2a.5.5 0 0 0-.5-.5h-4z"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endcan

                                    @can('usuarios.eliminar')
                                        <form action="{{ route('users.destroy', $user) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('¿Eliminar usuario?');">
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
                                                     stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6l-1 14H6L5 6"></path>
                                                    <path d="M10 11v6"></path>
                                                    <path d="M14 11v6"></path>
                                                    <path d="M9 6V4h6v2"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        @endcanany
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No hay registros</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection