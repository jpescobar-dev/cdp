@extends('layouts.theme.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Expedientes Presupuestarios</h1>
        <a href="{{ route('presupuesto.expedientes.create') }}" class="btn btn-primary">Nuevo expediente</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Correlativo</th>
                        <th>Año</th>
                        <th>Solicitante</th>
                        <th>Responsable</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expedientes as $expediente)
                        <tr>
                            <td>{{ $expediente->correlativo }}</td>
                            <td>{{ $expediente->anio }}</td>
                            <td>{{ $expediente->solicitante->nombre_completo ?? $expediente->solicitante_rut }}</td>
                            <td>{{ $expediente->responsable->nombre_completo ?? 'Sin asignar' }}</td>
                            <td>${{ number_format($expediente->monto, 0, ',', '.') }}</td>
                            <td><span class="badge bg-secondary">{{ $expediente->estado->nombre ?? 'Sin estado' }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('presupuesto.expedientes.show', $expediente) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No hay expedientes registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $expedientes->links() }}
        </div>
    </div>
</div>
@endsection
