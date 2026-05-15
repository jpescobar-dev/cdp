@extends('layouts.theme.app')

@section('title', 'Borradores CDP')

@section('content')
<div class="container-fluid py-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0">Borradores CDP</h1>
            <small class="text-muted">Certificados en preparación generados desde requerimientos Mesa de Ayuda.</small>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filtros --}}
    <form method="GET" class="card card-body mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">N° Requerimiento (folio)</label>
                <input type="text" name="folio" value="{{ request('folio') }}"
                       class="form-control" placeholder="Ej: 7958334">
            </div>
            <div class="col-md-4">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-control">
                    <option value="">Todos</option>
                    <option value="borrador"          @selected(request('estado') === 'borrador')>Borrador</option>
                    <option value="borrador_editado"  @selected(request('estado') === 'borrador_editado')>Borrador editado</option>
                    <option value="observado_usuario" @selected(request('estado') === 'observado_usuario')>Observado</option>
                    <option value="aprobado"          @selected(request('estado') === 'aprobado')>Aprobado</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" type="submit">Filtrar</button>
            </div>
            @if(request()->hasAny(['folio', 'estado']))
                <div class="col-md-2">
                    <a href="{{ route('mesa-ayuda.cdp-borradores.index') }}" class="btn btn-outline-secondary w-100">Limpiar</a>
                </div>
            @endif
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>N° Req.</th>
                        <th>Iniciativa</th>
                        <th>Cuenta presup.</th>
                        <th>Monto</th>
                        <th>Carácter</th>
                        <th>Validez</th>
                        <th>Estado</th>
                        <th>Aprobado</th>
                        <th>Creado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($borradores as $borrador)
                        <tr>
                            <td>
                                <strong>{{ $borrador->numero_requerimiento ?? '—' }}</strong>
                                @if($borrador->requerimientoMesaAyuda)
                                    <br>
                                    <a href="{{ route('mesa-ayuda.requerimientos.show', $borrador->requerimientoMesaAyuda) }}"
                                       class="small text-muted">ver req.</a>
                                @endif
                            </td>
                            <td style="max-width:260px">
                                <span class="d-inline-block text-truncate" style="max-width:250px"
                                      title="{{ $borrador->nombre_iniciativa }}">
                                    {{ $borrador->nombre_iniciativa ?? '—' }}
                                </span>
                            </td>
                            <td>{{ $borrador->cuenta_presupuestaria ?? '—' }}</td>
                            <td>
                                @if($borrador->monto_impto_incluido)
                                    ${{ number_format($borrador->monto_impto_incluido, 0, ',', '.') }}
                                @else
                                    <span class="text-muted">Pendiente</span>
                                @endif
                            </td>
                            <td>{{ $borrador->caracter_gasto ?? '—' }}</td>
                            <td>{{ $borrador->validez ?? '—' }}</td>
                            <td>
                                @php
                                    $badge = match($borrador->estado) {
                                        'aprobado'          => 'success',
                                        'observado_usuario' => 'warning',
                                        'borrador_editado'  => 'info',
                                        default             => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $badge }}">
                                    {{ str_replace('_', ' ', $borrador->estado) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($borrador->aprobado_por_usuario)
                                    <span class="text-success" title="Aprobado el {{ optional($borrador->fecha_aprobacion)->format('d-m-Y') }}">
                                        &#10003;
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ optional($borrador->created_at)->format('d-m-Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('mesa-ayuda.cdp-borradores.show', $borrador) }}"
                                   class="btn btn-sm btn-outline-primary">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                No hay borradores CDP registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $borradores->links() }}
    </div>

</div>
@endsection
