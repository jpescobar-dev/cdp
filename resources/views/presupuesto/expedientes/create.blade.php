@extends('layouts.theme.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Nuevo Expediente Presupuestario</h1>
        <a href="{{ route('presupuesto.expedientes.index') }}" class="btn btn-outline-secondary">Volver</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('presupuesto.expedientes.store') }}" class="card">
        @csrf
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Solicitante</label>
                    <select name="solicitante_rut" class="form-select" required>
                        <option value="">Seleccione</option>
                        @foreach($funcionarios as $funcionario)
                            <option value="{{ $funcionario->rut }}" @selected(old('solicitante_rut') == $funcionario->rut)>
                                {{ $funcionario->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Responsable / Revisor-Emisor</label>
                    <select name="responsable_rut" class="form-select">
                        <option value="">Sin asignar</option>
                        @foreach($funcionarios as $funcionario)
                            <option value="{{ $funcionario->rut }}" @selected(old('responsable_rut') == $funcionario->rut)>
                                {{ $funcionario->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Centro de costo</label>
                    <select name="ccosto" class="form-select" required>
                        <option value="">Seleccione</option>
                        @foreach($ccostos as $ccosto)
                            <option value="{{ $ccosto->ccosto }}" @selected(old('ccosto') == $ccosto->ccosto)>
                                {{ $ccosto->ccosto }} - {{ $ccosto->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Centro financiero</label>
                    <select name="cfinanciero" class="form-select">
                        <option value="">Seleccione</option>
                        @foreach($cfinancieros as $cfinanciero)
                            <option value="{{ $cfinanciero->cfinanciero }}" @selected(old('cfinanciero') == $cfinanciero->cfinanciero)>
                                {{ $cfinanciero->cfinanciero }} - {{ $cfinanciero->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Cuenta presupuestaria</label>
                    <input type="text" name="cuenta_presupuestaria" class="form-control" value="{{ old('cuenta_presupuestaria') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Denominación</label>
                    <input type="text" name="denominacion" class="form-control" value="{{ old('denominacion') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Monto</label>
                    <input type="number" name="monto" class="form-control" value="{{ old('monto') }}" min="1" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Moneda</label>
                    <input type="text" name="moneda" class="form-control" value="{{ old('moneda', 'CLP') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">N° Requerimiento</label>
                    <input type="text" name="numero_requerimiento" class="form-control" value="{{ old('numero_requerimiento') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Carácter del gasto</label>
                    <input type="text" name="caracter_gasto" class="form-control" value="{{ old('caracter_gasto') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Medio de solicitud</label>
                    <input type="text" name="medio_solicitud" class="form-control" value="{{ old('medio_solicitud') }}">
                </div>

                <div class="col-12">
                    <label class="form-label">Glosa</label>
                    <textarea name="glosa" class="form-control" rows="4" required>{{ old('glosa') }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">Guardar expediente</button>
        </div>
    </form>
</div>
@endsection
