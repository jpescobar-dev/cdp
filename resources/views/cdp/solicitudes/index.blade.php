@extends('layouts.theme.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Mis solicitudes de CDP</h1>
        <a href="{{ route('cdp.solicitudes.create') }}" class="btn btn-primary">
            <i data-feather="plus" style="width:16px;height:16px;" class="me-1"></i>
            Nueva solicitud
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Requirente</th>
                            <th>Proveedor</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitudes as $solicitud)
                            <tr>
                                <td>{{ $solicitud->id }}</td>
                                <td>
                                    <div>{{ $solicitud->nombre_requirente }}</div>
                                    <small class="text-muted">{{ $solicitud->unidad_requirente }}</small>
                                </td>
                                <td>{{ $solicitud->proveedor }}</td>
                                <td>{{ $solicitud->montoFormateado() }}</td>
                                <td>
                                    @if($solicitud->estado === 'pdf_generado')
                                        <span class="badge bg-success">PDF generado</span>
                                    @else
                                        <span class="badge bg-secondary">Borrador</span>
                                    @endif
                                </td>
                                <td>{{ $solicitud->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('cdp.solicitudes.show', $solicitud) }}"
                                       class="btn btn-sm btn-outline-primary">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No hay solicitudes registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($solicitudes->hasPages())
            <div class="card-footer">
                {{ $solicitudes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
