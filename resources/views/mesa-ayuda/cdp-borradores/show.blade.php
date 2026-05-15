@extends('layouts.theme.app')

@section('title', 'Borrador CDP — ' . ($borrador->numero_requerimiento ?? $borrador->id))

@section('styles')
<style>
/* El tema usa border !important en .form-control, por eso hay que forzar is-invalid */
.form-control.is-invalid,
select.form-control.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.15rem rgba(220, 53, 69, 0.25) !important;
}
</style>
@endsection

@section('content')

@php
    $aprobado  = $borrador->estado === 'aprobado';
    $rechazado = $borrador->estado === 'rechazado';
    $editable  = !$aprobado;

    $badgeColor = match($borrador->estado) {
        'aprobado'          => 'success',
        'rechazado'         => 'danger',
        'observado_usuario' => 'warning',
        'borrador_editado'  => 'info',
        default             => 'secondary',
    };

    // Construir timeline
    $timeline = [];
    $timeline[] = [
        'tipo'    => 'creado',
        'texto'   => 'Borrador generado automáticamente desde el requerimiento.',
        'usuario' => null,
        'fecha'   => $borrador->created_at?->toISOString(),
    ];
    foreach ($borrador->advertencias ?? [] as $ev) {
        if (is_array($ev) && isset($ev['fecha'])) {
            $timeline[] = $ev;
        }
    }
    usort($timeline, fn($a, $b) => strcmp($a['fecha'] ?? '', $b['fecha'] ?? ''));

    $iconoTipo = [
        'creado'            => ['color' => 'secondary', 'icon' => '&#9673;'],
        'aprobacion'        => ['color' => 'success',   'icon' => '&#10003;'],
        'rechazo'           => ['color' => 'danger',    'icon' => '&#10007;'],
        'observacion_usuario' => ['color' => 'warning', 'icon' => '&#9998;'],
        'nota'              => ['color' => 'info',      'icon' => '&#9432;'],
    ];

    // Faltantes calculados en tiempo real desde el estado actual del borrador
    $faltantesActuales = \App\Models\CdpBorrador::calcularDatosFaltantes($borrador->toArray());
    $hayFaltantes      = count($faltantesActuales) > 0;
@endphp

<div class="container-fluid py-3">

    {{-- Cabecera --}}
    <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
        <div>
            <h1 class="h4 mb-1">
                Borrador CDP
                <span class="badge bg-{{ $badgeColor }} fs-6 align-middle">
                    {{ str_replace('_', ' ', ucfirst($borrador->estado)) }}
                </span>
            </h1>
            <small class="text-muted">
                Requerimiento N° {{ $borrador->numero_requerimiento ?? '—' }}
                @if($borrador->requerimientoMesaAyuda)
                    &mdash;
                    <a href="{{ route('mesa-ayuda.requerimientos.show', $borrador->requerimientoMesaAyuda) }}">
                        Ver requerimiento
                    </a>
                @endif
                @if($borrador->expedientePresupuestario)
                    &mdash;
                    <a href="{{ route('presupuesto.expedientes.show', $borrador->expedientePresupuestario) }}">
                        Expediente {{ $borrador->expedientePresupuestario->correlativo }}
                    </a>
                @endif
            </small>
        </div>
        <a href="{{ route('mesa-ayuda.cdp-borradores.index') }}" class="btn btn-outline-secondary btn-sm">
            Volver al listado
        </a>
    </div>

    {{-- Alertas --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Aviso datos faltantes --}}
    @if(!$aprobado && count($borrador->datos_faltantes ?? []) > 0)
        <div class="alert alert-warning d-flex align-items-start gap-2 mb-3">
            <span style="font-size:1.2rem">&#9888;</span>
            <div>
                <strong>Faltan datos antes de aprobar:</strong>
                <span class="ms-1">
                    {{ implode(', ', array_map(fn($d) => str_replace('_', ' ', $d), $borrador->datos_faltantes)) }}
                </span>
            </div>
        </div>
    @endif

    <div class="row g-3">

        {{-- Col izquierda: formulario de datos --}}
        <div class="col-lg-8">
            <form method="POST" action="{{ route('mesa-ayuda.cdp-borradores.update', $borrador) }}" id="form-borrador">
                @csrf
                @method('PATCH')

                <div class="card mb-3">
                    <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                        Datos del certificado
                        @if($aprobado)
                            <span class="badge bg-success">Aprobado — solo lectura</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-3">
                                <label class="form-label">Número CDP</label>
                                <input name="numero_cdp" class="form-control" {{ $aprobado ? 'readonly' : '' }}
                                       value="{{ old('numero_cdp', $borrador->numero_cdp) }}"
                                       placeholder="{{ now()->year }}-001">
                                @if(!$aprobado)
                                    <small class="text-muted">Formato: {{ now()->year }}-NNN</small>
                                @endif
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Fecha emisión</label>
                                <input type="date" name="fecha_emision" class="form-control" {{ $aprobado ? 'readonly' : '' }}
                                       value="{{ old('fecha_emision', optional($borrador->fecha_emision)->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">CF</label>
                                <input name="cf" class="form-control" {{ $aprobado ? 'readonly' : '' }}
                                       value="{{ old('cf', $borrador->cf) }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">ST <span class="text-danger">*</span></label>
                                @if($aprobado)
                                    <input class="form-control" readonly value="{{ $borrador->st }}">
                                @else
                                    <select name="st" class="form-control" required>
                                        <option value="">—</option>
                                        <option value="22" @selected(old('st', $borrador->st) === '22')>22</option>
                                        <option value="29" @selected(old('st', $borrador->st) === '29')>29</option>
                                        <option value="31" @selected(old('st', $borrador->st) === '31')>31</option>
                                    </select>
                                @endif
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tipo gasto <span class="text-danger">*</span></label>
                                @if($aprobado)
                                    <input class="form-control" readonly value="{{ $borrador->tipo_gasto }}">
                                @else
                                    <select name="tipo_gasto" id="tipo_gasto_select" class="form-control" required>
                                        <option value="GO"  @selected(old('tipo_gasto', $borrador->tipo_gasto ?? 'GO') === 'GO')>Gasto Operativo</option>
                                        <option value="INI" @selected(old('tipo_gasto', $borrador->tipo_gasto) === 'INI')>Iniciativa</option>
                                    </select>
                                @endif
                            </div>

                            <div class="col-12" id="seccion-iniciativa"
                                 style="{{ (old('tipo_gasto', $borrador->tipo_gasto ?? 'GO') === 'INI') ? '' : 'display:none' }}">
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label class="form-label">Nombre iniciativa</label>
                                        <textarea name="nombre_iniciativa" class="form-control" rows="2"
                                                  {{ $aprobado ? 'readonly' : '' }}>{{ old('nombre_iniciativa', $borrador->nombre_iniciativa) }}</textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Código iniciativa</label>
                                        <input name="codigo_iniciativa" class="form-control" {{ $aprobado ? 'readonly' : '' }}
                                               value="{{ old('codigo_iniciativa', $borrador->codigo_iniciativa) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <label class="form-label">Cuenta presupuestaria <span class="text-danger">*</span></label>
                                @if($aprobado)
                                    <input class="form-control" readonly value="{{ $borrador->cuenta_presupuestaria }}">
                                @else
                                    <select name="cuenta_presupuestaria" id="cuenta_select" class="form-control" required>
                                        <option value="">— Seleccione catálogo —</option>
                                        @foreach($catalogos as $cat)
                                            <option value="{{ $cat->catalogo }}"
                                                    data-nombre="{{ $cat->nombre }}"
                                                    @selected(old('cuenta_presupuestaria', $borrador->cuenta_presupuestaria) === $cat->catalogo)>
                                                {{ $cat->catalogo }} — {{ $cat->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="col-md-7">
                                <label class="form-label">Denominación <span class="text-danger">*</span></label>
                                <input name="denominacion" id="denominacion_input" class="form-control bg-light" readonly
                                       value="{{ old('denominacion', $borrador->denominacion) }}"
                                       placeholder="Se completa al seleccionar el catálogo">
                                <small class="text-muted">Se llena automáticamente al seleccionar la cuenta.</small>
                            </div>

                            <div class="col-md-5">
                                <label class="form-label">Unidad requirente</label>
                                @if($aprobado)
                                    <input class="form-control" readonly value="{{ $borrador->ccosto_requirente }}">
                                @else
                                    <select name="ccosto_requirente" class="form-control">
                                        <option value="">— Seleccione unidad —</option>
                                        @foreach($ccostos as $cc)
                                            <option value="{{ $cc->ccosto }}"
                                                    @selected(old('ccosto_requirente', $borrador->ccosto_requirente) === $cc->ccosto)>
                                                {{ $cc->ccosto }} — {{ $cc->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Unidad ejecutora</label>
                                <input name="unidad_ejecutora" class="form-control" {{ $aprobado ? 'readonly' : '' }}
                                       value="{{ old('unidad_ejecutora', $borrador->unidad_ejecutora) }}">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">N° UE</label>
                                <input name="numero_ue" class="form-control" {{ $aprobado ? 'readonly' : '' }}
                                       value="{{ old('numero_ue', $borrador->numero_ue) }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Validez <span class="text-danger">*</span></label>
                                <input type="date" name="validez" class="form-control" {{ $aprobado ? 'readonly' : '' }} required
                                       value="{{ old('validez', $borrador->validez ?: '2026-12-31') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Carácter gasto <span class="text-danger">*</span></label>
                                @if($aprobado)
                                    <input class="form-control" readonly value="{{ $borrador->caracter_gasto }}">
                                @else
                                    <select name="caracter_gasto" class="form-control" required>
                                        <option value="">— Seleccione —</option>
                                        <option value="TRANSITORIO" @selected(old('caracter_gasto', $borrador->caracter_gasto) === 'TRANSITORIO')>Transitorio</option>
                                        <option value="PERMANENTE" @selected(old('caracter_gasto', $borrador->caracter_gasto) === 'PERMANENTE')>Permanente</option>
                                    </select>
                                @endif
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Monto imp. incluido</label>
                                <input type="number" step="1" name="monto_impto_incluido" id="show-monto"
                                       class="form-control" {{ $aprobado ? 'readonly' : '' }}
                                       value="{{ old('monto_impto_incluido', $borrador->monto_impto_incluido) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Moneda compra</label>
                                @if($aprobado)
                                    <input class="form-control" readonly value="{{ $borrador->moneda_compra }}">
                                @else
                                    <select name="moneda_compra" id="show-moneda-compra" class="form-control">
                                        <option value="">— Seleccione —</option>
                                        <option value="CLP"   @selected(old('moneda_compra', $borrador->moneda_compra) === 'CLP')>CLP — Peso chileno</option>
                                        <option value="UF"    @selected(old('moneda_compra', $borrador->moneda_compra) === 'UF')>UF — Unidad de Fomento</option>
                                        <option value="Dolar" @selected(old('moneda_compra', $borrador->moneda_compra) === 'Dolar')>Dólar</option>
                                    </select>
                                @endif
                            </div>
                            <div class="col-md-3" id="show-grupo-fecha-paridad"
                                 style="{{ (old('moneda_compra', $borrador->moneda_compra) === 'UF' && !$aprobado) ? '' : 'display:none' }}">
                                <label class="form-label">Fecha paridad UF</label>
                                <input type="date" name="fecha_paridad" id="show-fecha-paridad"
                                       class="form-control" {{ $aprobado ? 'readonly' : '' }}
                                       value="{{ old('fecha_paridad', optional($borrador->fecha_paridad)->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-3" id="show-grupo-valor-paridad"
                                 style="{{ (!$aprobado && !in_array(old('moneda_compra', $borrador->moneda_compra), ['UF', 'Dolar'])) ? 'display:none' : '' }}">
                                <label class="form-label">Valor paridad</label>
                                @if($aprobado)
                                    <input class="form-control" readonly value="{{ $borrador->valor_paridad }}">
                                @else
                                    <input type="number" step="0.0001" name="valor_paridad" id="show-valor-paridad"
                                           class="form-control bg-light" readonly
                                           value="{{ old('valor_paridad', $borrador->valor_paridad) }}" placeholder="Auto">
                                @endif
                                <div id="show-paridad-mensaje" class="text-danger small mt-1" style="display:none"></div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Total moneda compra</label>
                                @if($aprobado)
                                    <input class="form-control" readonly value="{{ $borrador->total_moneda_compra }}">
                                @else
                                    <input type="number" step="0.0001" name="total_moneda_compra" id="show-total-moneda"
                                           class="form-control bg-light" readonly
                                           value="{{ old('total_moneda_compra', $borrador->total_moneda_compra) }}"
                                           placeholder="Calculado automáticamente">
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Medio solicitud</label>
                                <input name="medio_solicitud" class="form-control" {{ $aprobado ? 'readonly' : '' }}
                                       value="{{ old('medio_solicitud', $borrador->medio_solicitud) }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Texto certificación</label>
                                <textarea name="texto_certificacion" class="form-control" rows="5"
                                          {{ $aprobado ? 'readonly' : '' }}>{{ old('texto_certificacion', $borrador->texto_certificacion) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Respuesta Mesa de Ayuda sugerida</label>
                                <textarea name="respuesta_mesa_ayuda_borrador" class="form-control" rows="3"
                                          {{ $aprobado ? 'readonly' : '' }}>{{ old('respuesta_mesa_ayuda_borrador', $borrador->respuesta_mesa_ayuda_borrador) }}</textarea>
                            </div>

                        </div>
                    </div>
                </div>

                @if($editable)
                    <button class="btn btn-primary" type="submit">Guardar cambios</button>
                @endif
            </form>
        </div>

        {{-- Col derecha: aprobación + seguimiento --}}
        <div class="col-lg-4">

            {{-- Panel aprobación --}}
            <div class="card mb-3 border-{{ $badgeColor }}">
                <div class="card-header fw-semibold bg-{{ $badgeColor }} bg-opacity-10">
                    Aprobación del borrador
                </div>
                <div class="card-body">

                    @if($aprobado)
                        <div class="text-center py-2">
                            <div class="display-6 text-success mb-1">&#10003;</div>
                            <p class="mb-1 fw-semibold">Aprobado</p>
                            <small class="text-muted">
                                {{ optional($borrador->fecha_aprobacion)->format('d-m-Y H:i') }}
                            </small>
                        </div>

                    @else

                        {{-- Botón Aprobar: deshabilitado si hay datos faltantes --}}
                        <button type="button" id="btn-abrir-aprobar"
                                class="btn btn-success w-100 mb-1"
                                data-toggle="modal" data-target="#modal-aprobar"
                                {{ $hayFaltantes ? 'disabled' : '' }}>
                            &#10003; Aprobar borrador
                        </button>
                        <p id="msg-campos-requeridos" class="text-danger small text-center mb-2"
                           style="{{ $hayFaltantes ? '' : 'display:none' }}">
                            Complete los campos marcados en rojo antes de aprobar.
                        </p>

                        {{-- Botón Rechazar con modal --}}
                        <button type="button" class="btn btn-outline-danger w-100"
                                data-toggle="modal" data-target="#modal-rechazar">
                            &#10007; Rechazar
                        </button>

                    @endif
                </div>
            </div>

            {{-- Adjuntos del requerimiento --}}
            @php $adjuntos = $borrador->requerimientoMesaAyuda?->adjuntos ?? collect(); @endphp
            @if($adjuntos->isNotEmpty())
            <div class="card mb-3">
                <div class="card-header fw-semibold">Adjuntos del requerimiento</div>
                <div class="card-body py-2 px-3">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($adjuntos->where('descargado', true)->where('ruta_local', '!=', null) as $adj)
                            <a href="{{ route('mesa-ayuda.adjuntos.ver', $adj) }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                               title="{{ $adj->nombre_archivo }}">
                                @php
                                    $icono = match(true) {
                                        str_contains($adj->tipo_mime ?? '', 'pdf')   => '📄',
                                        str_contains($adj->tipo_mime ?? '', 'image') => '🖼',
                                        str_contains($adj->tipo_mime ?? '', 'word') || str_ends_with($adj->nombre_archivo, '.docx') => '📝',
                                        str_contains($adj->tipo_mime ?? '', 'excel') || str_ends_with($adj->nombre_archivo, '.xlsx') => '📊',
                                        default => '📎',
                                    };
                                @endphp
                                <span>{{ $icono }}</span>
                                <span class="text-truncate" style="max-width:120px; font-size:.8rem">{{ $adj->nombre_archivo }}</span>
                            </a>
                        @endforeach
                    </div>
                    @if($adjuntos->where('descargado', true)->where('ruta_local', '!=', null)->isEmpty())
                        <p class="text-muted small mb-0">No hay adjuntos descargados disponibles.</p>
                    @endif
                </div>
            </div>
            @endif

            {{-- Seguimiento / timeline --}}
            <div class="card mb-3">
                <div class="card-header fw-semibold">Seguimiento</div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($timeline as $ev)
                            @php
                                $ic = $iconoTipo[$ev['tipo']] ?? ['color' => 'secondary', 'icon' => '&#9673;'];
                                $fecha = \Carbon\Carbon::parse($ev['fecha'])->format('d-m-Y H:i');
                            @endphp
                            <li class="list-group-item px-3 py-2">
                                <div class="d-flex gap-2 align-items-start">
                                    <span class="text-{{ $ic['color'] }} mt-1" style="font-size:.9rem">{!! $ic['icon'] !!}</span>
                                    <div class="flex-grow-1">
                                        <div class="small text-muted">{{ $fecha }}@if($ev['usuario'] ?? null) &mdash; {{ $ev['usuario'] }}@endif</div>
                                        <div class="small">{{ $ev['texto'] }}</div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Modal: confirmar aprobación --}}
<div class="modal fade" id="modal-aprobar" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('mesa-ayuda.cdp-borradores.aprobar', $borrador) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Aprobar Certificado de Disponibilidad Presupuestaria</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">

                    @if(count($borrador->datos_faltantes ?? []) > 0)
                        <div class="alert alert-warning py-2 mb-3">
                            <strong>Atención:</strong> el borrador aún tiene datos incompletos
                            ({{ implode(', ', array_map(fn($d) => str_replace('_', ' ', $d), $borrador->datos_faltantes)) }}).
                            Complete el formulario antes de aprobar.
                        </div>
                    @endif

                    <p class="mb-3">Al aprobar este documento usted certifica formalmente, bajo su responsabilidad, que la institución cuenta con disponibilidad presupuestaria suficiente para financiar los bienes, servicios u obras indicados en este certificado.</p>

                    <div class="card border-success mb-3">
                        <div class="card-body py-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       id="check-disponibilidad"
                                       name="confirma_disponibilidad"
                                       value="1">
                                <label class="form-check-label fw-semibold" for="check-disponibilidad">
                                    Confirmo que existe disponibilidad presupuestaria para el presente certificado N° {{ $borrador->numero_cdp ?? '—' }}.
                                </label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="form-label text-muted small mb-1">Observación al aprobar <span class="fw-normal">(opcional)</span></label>
                        <textarea name="observacion_aprobacion"
                                  class="form-control form-control-sm"
                                  rows="3"
                                  placeholder="Puede agregar una nota que quedará registrada en el seguimiento…"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btn-confirmar-aprobar" class="btn btn-success" disabled>
                        &#10003; Certificar y aprobar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: rechazar con motivo --}}
<div class="modal fade" id="modal-rechazar" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('mesa-ayuda.cdp-borradores.rechazar', $borrador) }}">
                @csrf
                <div class="modal-header border-danger">
                    <h5 class="modal-title text-danger">Rechazar borrador</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">El borrador quedará en estado <strong>rechazado</strong>.
                       Indique el motivo para que el analista pueda corregir.</p>
                    <label class="form-label">Motivo del rechazo <span class="text-danger">*</span></label>
                    <textarea name="motivo" class="form-control" rows="4" required
                              placeholder="Describa qué debe corregirse…"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Rechazar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Denominación auto-fill (el listener con verificarCampos está abajo) ──
    const cuentaSelect      = document.getElementById('cuenta_select');
    const denominacionInput = document.getElementById('denominacion_input');

    // ── Checkbox disponibilidad: habilita botón aprobar ──────────────────
    const checkDisponibilidad = document.getElementById('check-disponibilidad');
    const btnAprobar          = document.getElementById('btn-confirmar-aprobar');
    if (checkDisponibilidad && btnAprobar) {
        checkDisponibilidad.addEventListener('change', function () {
            btnAprobar.disabled = !this.checked;
        });
        // Resetear al cerrar el modal (por si el usuario cancela y vuelve a abrir)
        $('#modal-aprobar').on('hidden.bs.modal', function () {
            checkDisponibilidad.checked = false;
            btnAprobar.disabled = true;
        });
    }

    // ── Toggle sección iniciativa ─────────────────────────────────────────
    const tipoGastoSelect   = document.getElementById('tipo_gasto_select');
    const seccionIniciativa = document.getElementById('seccion-iniciativa');

    function toggleIniciativa() {
        if (!tipoGastoSelect || !seccionIniciativa) return;
        seccionIniciativa.style.display = tipoGastoSelect.value === 'INI' ? '' : 'none';
    }
    if (tipoGastoSelect) tipoGastoSelect.addEventListener('change', toggleIniciativa);

    // ── Paridad UF ────────────────────────────────────────────────────────
    @unless($aprobado)
    const monedaSelect       = document.getElementById('show-moneda-compra');
    const grupoFechaParidad  = document.getElementById('show-grupo-fecha-paridad');
    const grupoValorParidad  = document.getElementById('show-grupo-valor-paridad');
    const fechaParidadInput  = document.getElementById('show-fecha-paridad');
    const valorParidadInput  = document.getElementById('show-valor-paridad');
    const totalMonedaInput   = document.getElementById('show-total-moneda');
    const montoInput         = document.getElementById('show-monto');
    const paridadMensaje     = document.getElementById('show-paridad-mensaje');

    function calcularTotal() {
        const monto   = parseFloat(montoInput?.value)        || 0;
        const paridad = parseFloat(valorParidadInput?.value) || 0;
        if (monto > 0 && paridad > 0 && totalMonedaInput) {
            totalMonedaInput.value = (monto * paridad).toFixed(4);
        } else if (totalMonedaInput) {
            totalMonedaInput.value = '';
        }
    }

    async function buscarParidadUf(fecha) {
        if (!paridadMensaje) return;
        paridadMensaje.style.display = 'none';
        paridadMensaje.textContent   = '';
        paridadMensaje.className     = 'small mt-1';
        if (valorParidadInput) valorParidadInput.value = '';
        if (totalMonedaInput)  totalMonedaInput.value  = '';

        if (!fecha) { verificarCampos(); return; }

        paridadMensaje.textContent   = 'Consultando valor UF…';
        paridadMensaje.className     = 'small mt-1 text-muted';
        paridadMensaje.style.display = '';

        try {
            const url  = '{{ route('mesa-ayuda.api.paridad-uf') }}?fecha=' + fecha;
            const resp = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await resp.json();

            if (!resp.ok || data.error) {
                paridadMensaje.textContent   = data.error ?? 'Error al consultar el valor UF.';
                paridadMensaje.className     = 'small mt-1 text-danger';
                paridadMensaje.style.display = '';
            } else {
                if (valorParidadInput) valorParidadInput.value = data.valor;
                calcularTotal();
                if (data.advertencia) {
                    paridadMensaje.textContent   = '⚠ ' + data.advertencia;
                    paridadMensaje.className     = 'small mt-1 text-warning';
                    paridadMensaje.style.display = '';
                } else {
                    paridadMensaje.style.display = 'none';
                }
            }
        } catch (e) {
            paridadMensaje.textContent   = 'No se pudo conectar para consultar el valor UF.';
            paridadMensaje.className     = 'small mt-1 text-danger';
            paridadMensaje.style.display = '';
        }
        verificarCampos();
    }

    function aplicarMoneda(moneda) {
        if (!grupoFechaParidad) return;
        if (moneda === 'CLP') {
            grupoFechaParidad.style.display = 'none';
            if (grupoValorParidad)  grupoValorParidad.style.display = 'none';
            if (fechaParidadInput)  fechaParidadInput.value  = '';
            if (valorParidadInput)  { valorParidadInput.value = '1'; valorParidadInput.readOnly = true; }
            calcularTotal();
        } else if (moneda === 'UF') {
            grupoFechaParidad.style.display = '';
            if (grupoValorParidad)  grupoValorParidad.style.display = '';
            if (valorParidadInput)  valorParidadInput.readOnly = true;
            // Solo buscar UF si no hay valor guardado (evita limpiar datos al cargar la página)
            if (fechaParidadInput?.value && !valorParidadInput?.value) {
                buscarParidadUf(fechaParidadInput.value);
            }
        } else if (moneda === 'Dolar') {
            grupoFechaParidad.style.display = 'none';
            if (grupoValorParidad)  grupoValorParidad.style.display = '';
            if (fechaParidadInput)  fechaParidadInput.value  = '';
            if (valorParidadInput)  { valorParidadInput.value = ''; valorParidadInput.readOnly = false; }
            if (totalMonedaInput)   totalMonedaInput.readOnly = false;
        } else {
            grupoFechaParidad.style.display = 'none';
            if (grupoValorParidad)  grupoValorParidad.style.display = 'none';
            if (fechaParidadInput)  fechaParidadInput.value  = '';
            if (valorParidadInput)  { valorParidadInput.value = ''; valorParidadInput.readOnly = false; }
            if (totalMonedaInput)   totalMonedaInput.readOnly = false;
        }
    }

    if (monedaSelect) {
        monedaSelect.addEventListener('change', function () {
            aplicarMoneda(this.value);
            verificarCampos();
        });
        aplicarMoneda(monedaSelect.value);
    }

    if (fechaParidadInput) {
        fechaParidadInput.addEventListener('change', function () {
            if (monedaSelect?.value === 'UF') buscarParidadUf(this.value);
        });
    }

    if (montoInput) {
        montoInput.addEventListener('input', function () {
            if (['CLP', 'UF'].includes(monedaSelect?.value) && valorParidadInput?.value) {
                calcularTotal();
            }
            verificarCampos();
        });
    }

    // ── Validación en tiempo real de campos requeridos ────────────────────
    const selectoresCamposReq = [
        '[name="st"]',
        '[name="cuenta_presupuestaria"]',
        '#show-monto',
        '#show-moneda-compra',
    ];

    function verificarCampos() {
        let completo = true;

        selectoresCamposReq.forEach(sel => {
            const el = document.querySelector(sel);
            if (!el) return;
            const vacio = !el.value || el.value === '';
            el.classList.toggle('is-invalid', vacio);
            if (vacio) completo = false;
        });

        const btnAbrir  = document.getElementById('btn-abrir-aprobar');
        const msgReq    = document.getElementById('msg-campos-requeridos');
        if (btnAbrir)  btnAbrir.disabled  = !completo;
        if (msgReq)    msgReq.style.display = completo ? 'none' : '';
    }

    // Escuchar cambios en todos los campos requeridos
    selectoresCamposReq.forEach(sel => {
        const el = document.querySelector(sel);
        if (!el) return;
        el.addEventListener('change', verificarCampos);
        el.addEventListener('input',  verificarCampos);
    });

    // Auto-fill cuenta → también verifica denominación
    if (cuentaSelect) {
        cuentaSelect.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (denominacionInput) denominacionInput.value = opt.value ? (opt.dataset.nombre ?? '') : '';
            verificarCampos();
        });
    }

    // Estado inicial al cargar
    verificarCampos();
    @endunless
});
</script>
@endsection
