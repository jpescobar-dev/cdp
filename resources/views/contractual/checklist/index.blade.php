@extends('layouts.theme.app')

@section('content')
<div class="widget-content widget-content-area br-6 mt-2 mb-2">

    <div class="d-flex justify-content-between mb-3">
        <h4>Checklist Snapshot v{{ $snapshot->numero_version }}</h4>

        <a href="{{ route('contractual.revisiones.snapshots.checklist.create', [$revision, $snapshot]) }}"
           class="btn btn-primary btn-sm">
            Nuevo ítem
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-3" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Ítem</th>
                <th>Estado</th>
                <th>Observación</th>
                <th>Referencia</th>
            </tr>
        </thead>
        <tbody>
            @forelse($snapshot->checklist as $item)
                <tr>
                    <td>{{ $item->item }}</td>
                    <td>{{ $item->estado_item }}</td>
                    <td>{{ $item->observacion }}</td>
                    <td>{{ $item->referencia_documental }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">No existen ítems de checklist registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>
@endsection
