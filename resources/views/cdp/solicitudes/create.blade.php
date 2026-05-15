@extends('layouts.theme.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Nueva solicitud de CDP</h1>
        <a href="{{ route('cdp.solicitudes.index') }}" class="btn btn-outline-secondary">Volver</a>
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

    <form method="POST"
          action="{{ route('cdp.solicitudes.store') }}"
          enctype="multipart/form-data">
        @csrf

        {{-- Datos del requirente --}}
        <div class="card mb-4">
            <div class="card-header fw-semibold">1. Datos del requirente</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
                        <input type="text"
                               name="nombre_requirente"
                               class="form-control @error('nombre_requirente') is-invalid @enderror"
                               value="{{ old('nombre_requirente') }}"
                               required>
                        @error('nombre_requirente')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">RUT <span class="text-danger">*</span></label>
                        <input type="text"
                               name="rut_requirente"
                               class="form-control @error('rut_requirente') is-invalid @enderror"
                               value="{{ old('rut_requirente') }}"
                               placeholder="12.345.678-9"
                               maxlength="12"
                               required>
                        @error('rut_requirente')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Unidad / Departamento <span class="text-danger">*</span></label>
                        <input type="text"
                               name="unidad_requirente"
                               class="form-control @error('unidad_requirente') is-invalid @enderror"
                               value="{{ old('unidad_requirente') }}"
                               required>
                        @error('unidad_requirente')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Centro de costo</label>
                        @if($ccostos->isNotEmpty())
                            <select name="ccosto"
                                    class="form-select @error('ccosto') is-invalid @enderror">
                                <option value="">— Seleccione —</option>
                                @foreach($ccostos as $cc)
                                    <option value="{{ $cc->ccosto }}"
                                        @selected(old('ccosto') === $cc->ccosto)>
                                        {{ $cc->ccosto }} — {{ $cc->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input type="text"
                                   name="ccosto"
                                   class="form-control @error('ccosto') is-invalid @enderror"
                                   value="{{ old('ccosto') }}"
                                   placeholder="Ej: 1001"
                                   maxlength="10">
                            <div class="form-text text-muted">Ingrese el código manualmente (catálogo no disponible).</div>
                        @endif
                        @error('ccosto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">N° Requerimiento</label>
                        <input type="text"
                               name="requerimiento"
                               class="form-control @error('requerimiento') is-invalid @enderror"
                               value="{{ old('requerimiento') }}"
                               placeholder="Ej: REQ-2026-001">
                        @error('requerimiento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Referencia interna del requerimiento (opcional).</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Datos del gasto --}}
        <div class="card mb-4">
            <div class="card-header fw-semibold">2. Datos del gasto</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Descripción del gasto (glosa) <span class="text-danger">*</span></label>
                        <textarea name="glosa"
                                  rows="4"
                                  class="form-control @error('glosa') is-invalid @enderror"
                                  maxlength="2000"
                                  required>{{ old('glosa') }}</textarea>
                        @error('glosa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Describa con detalle el bien o servicio a adquirir.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Proveedor <span class="text-danger">*</span></label>
                        <input type="text"
                               name="proveedor"
                               class="form-control @error('proveedor') is-invalid @enderror"
                               value="{{ old('proveedor') }}"
                               required>
                        @error('proveedor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Monto estimado <span class="text-danger">*</span></label>
                        <input type="number"
                               name="monto_estimado"
                               class="form-control @error('monto_estimado') is-invalid @enderror"
                               value="{{ old('monto_estimado') }}"
                               min="1"
                               step="1"
                               required>
                        @error('monto_estimado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Moneda <span class="text-danger">*</span></label>
                        <select name="moneda"
                                class="form-select @error('moneda') is-invalid @enderror"
                                required>
                            <option value="CLP" @selected(old('moneda', 'CLP') === 'CLP')>CLP (Pesos)</option>
                            <option value="UF"  @selected(old('moneda') === 'UF')>UF</option>
                        </select>
                        @error('moneda')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tipo de gasto</label>
                        <select name="tipo_gasto1"
                                class="form-select @error('tipo_gasto1') is-invalid @enderror"
                                id="tipo_gasto1">
                            <option value="">— Seleccione —</option>
                            <option value="GO"  @selected(old('tipo_gasto1') === 'GO')>GO — Gasto Operacional</option>
                            <option value="INI" @selected(old('tipo_gasto1') === 'INI')>INI — Inversión</option>
                        </select>
                        @error('tipo_gasto1')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Clasificación del gasto</label>
                        <select name="tipo_gasto2"
                                class="form-select @error('tipo_gasto2') is-invalid @enderror">
                            <option value="">— Seleccione —</option>
                            <option value="TRANSITORIO"  @selected(old('tipo_gasto2') === 'TRANSITORIO')>Transitorio</option>
                            <option value="PERMANENTE"   @selected(old('tipo_gasto2') === 'PERMANENTE')>Permanente</option>
                        </select>
                        @error('tipo_gasto2')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4" id="proyecto-field">
                        <label class="form-label">Proyecto</label>
                        @if($proyectos->isNotEmpty())
                            <select name="proyecto_id"
                                    class="form-select @error('proyecto_id') is-invalid @enderror">
                                <option value="">— Seleccione —</option>
                                @foreach($proyectos as $proy)
                                    <option value="{{ $proy->id }}"
                                        @selected(old('proyecto_id') == $proy->id)>
                                        {{ $proy->codigo }} — {{ $proy->proyecto }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" class="form-control" disabled placeholder="Sin proyectos registrados">
                        @endif
                        @error('proyecto_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Solo requerido para gastos de tipo Inversión (INI).</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Documentos adjuntos --}}
        <div class="card mb-4">
            <div class="card-header fw-semibold">3. Documentos de respaldo</div>
            <div class="card-body">
                <label class="form-label">Adjuntar archivos</label>
                <input type="file"
                       name="documentos[]"
                       class="form-control @error('documentos') is-invalid @enderror @error('documentos.*') is-invalid @enderror"
                       multiple
                       accept=".pdf,.jpg,.jpeg,.png,.docx,.xlsx">
                @error('documentos')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                @error('documentos.*')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <div class="form-text">
                    Formatos aceptados: PDF, JPG, PNG, DOCX, XLSX. Máximo 5 archivos, 10 MB cada uno.
                    (Cotizaciones, presupuestos u otros documentos de respaldo)
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('cdp.solicitudes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">
                <i data-feather="file-text" style="width:16px;height:16px;" class="me-1"></i>
                Enviar y generar PDF
            </button>
        </div>
    </form>
</div>
@endsection
