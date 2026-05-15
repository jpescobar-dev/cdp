@extends('layouts.theme.app')

@section('title', 'Detalle requerimiento Mesa de Ayuda')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h1 class="h4 mb-0">Requerimiento N° {{ $requerimiento->folio }}</h1>
            <small class="text-muted">Estado externo: {{ $requerimiento->estado_externo ?? 'No informado' }}</small>
        </div>
        <a href="{{ route('mesa-ayuda.requerimientos.index') }}" class="btn btn-outline-secondary btn-sm">Volver</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header fw-semibold">Cabecera</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Fecha/Hora</dt>
                        <dd class="col-sm-9">{{ optional($requerimiento->fecha_hora)->format('d-m-Y H:i') ?? '—' }}</dd>

                        <dt class="col-sm-3">Componente</dt>
                        <dd class="col-sm-9">{{ $requerimiento->componente ?? '—' }}</dd>

                        <dt class="col-sm-3">Tipo requerimiento</dt>
                        <dd class="col-sm-9">{{ $requerimiento->tipo_requerimiento ?? '—' }}</dd>

                        <dt class="col-sm-3">Tribunal</dt>
                        <dd class="col-sm-9">{{ $requerimiento->tribunal ?? '—' }}</dd>

                        <dt class="col-sm-3">Solicitado por</dt>
                        <dd class="col-sm-9">{{ $requerimiento->solicitado_por ?? '—' }}</dd>

                        <dt class="col-sm-3">Solicitado para</dt>
                        <dd class="col-sm-9">{{ $requerimiento->solicitado_para ?? '—' }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header fw-semibold">Observación principal</div>
                <div class="card-body">
                    <p class="mb-0" style="white-space: pre-line">{{ $requerimiento->observacion_principal ?? 'Sin observación principal registrada.' }}</p>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header fw-semibold">Historial externo capturado</div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Estado</th>
                                <th>Acción</th>
                                <th>Usuario</th>
                                <th>Observación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requerimiento->historial as $item)
                                <tr>
                                    <td>{{ optional($item->fecha)->format('d-m-Y') ?? '—' }}</td>
                                    <td>{{ $item->hora ?? '—' }}</td>
                                    <td>{{ $item->estado_externo ?? '—' }}</td>
                                    <td>{{ $item->accion ?? '—' }}</td>
                                    <td>{{ $item->usuario_externo ?? '—' }}</td>
                                    <td style="white-space: pre-line">{{ $item->observacion ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-3">Sin historial registrado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header fw-semibold">Adjuntos capturados</div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Archivo</th>
                                <th>Tipo</th>
                                <th>Descargado</th>
                                <th>Clasificación</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requerimiento->adjuntos as $adjunto)
                                <tr>
                                    <td>{{ $adjunto->nombre_archivo }}</td>
                                    <td>{{ $adjunto->tipo_mime ?? '—' }}</td>
                                    <td>{{ $adjunto->descargado ? 'Sí' : 'No' }}</td>
                                    <td>{{ $adjunto->clasificacion_documento ?? 'Pendiente' }}</td>
                                    <td class="text-end">
                                        @if($adjunto->descargado && $adjunto->ruta_local)
                                            <a href="{{ route('mesa-ayuda.adjuntos.ver', $adjunto) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-outline-secondary"
                                               title="Ver adjunto">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                                                </svg>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">Sin adjuntos registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header fw-semibold">Clasificación</div>
                <div class="card-body">
                    <p><strong>Clasificación:</strong><br>{{ $requerimiento->clasificacion ?? 'Sin clasificar' }}</p>
                    <p><strong>Confianza:</strong> {{ $requerimiento->confianza_clasificacion ?? '—' }}</p>
                    <p><strong>Score:</strong> {{ $requerimiento->score_clasificacion }}</p>
                    <p><strong>Destino:</strong><br>{{ $requerimiento->destino_flujo ?? '—' }}</p>

                    <form method="POST" action="{{ route('mesa-ayuda.requerimientos.reclasificar', $requerimiento) }}" class="mb-2">
                        @csrf
                        <button class="btn btn-outline-primary w-100">Reclasificar</button>
                    </form>

                    @if($requerimiento->requiere_cdp)
                        <form method="POST" action="{{ route('mesa-ayuda.requerimientos.crear-expediente', $requerimiento) }}" class="mb-2">
                            @csrf
                            <button class="btn btn-outline-success w-100" @disabled($requerimiento->expediente_presupuestario_id)>Crear expediente presupuestario</button>
                        </form>

                        @if($requerimiento->cdpBorradores->isNotEmpty())
                            <a href="{{ route('mesa-ayuda.cdp-borradores.show', $requerimiento->cdpBorradores->first()) }}"
                               class="btn btn-outline-success w-100">
                                Ver borrador CDP existente
                            </a>
                        @else
                            <button type="button" class="btn btn-success w-100"
                                    data-toggle="modal" data-target="#modal-generar-borrador">
                                Generar borrador CDP
                            </button>
                        @endif
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header fw-semibold">Tipificación</div>
                <div class="card-body">
                    <p class="mb-0">{{ $requerimiento->tipificacion ?? 'No informada' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Modal: Generar borrador CDP --}}
<div class="modal fade" id="modal-generar-borrador" tabindex="-1" role="dialog" aria-labelledby="modal-generar-borrador-label">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modal-generar-borrador-label">
                    Generar borrador CDP — Requerimiento N° {{ $requerimiento->folio }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form method="POST" action="{{ route('mesa-ayuda.requerimientos.generar-cdp-borrador', $requerimiento) }}" id="form-generar-borrador">
                @csrf
                <div class="modal-body" style="max-height:72vh; overflow-y:auto;">

                    @if($errors->any())
                        <div class="alert alert-danger py-2 mb-3">
                            <ul class="mb-0 pl-3">
                                @foreach($errors->all() as $e)<li class="small">{{ $e }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Fila 1: Número CDP | Fecha emisión | CF | ST | Tipo gasto --}}
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label>Número CDP</label>
                            <input type="text" name="numero_cdp" id="modal-numero-cdp"
                                   class="form-control bg-light @error('numero_cdp') is-invalid @enderror"
                                   readonly value="{{ old('numero_cdp') }}" placeholder="Generando…">
                            <small class="text-muted">Generado automáticamente.</small>
                            @error('numero_cdp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Fecha emisión</label>
                            <input type="date" name="fecha_emision"
                                   class="form-control @error('fecha_emision') is-invalid @enderror"
                                   value="{{ old('fecha_emision', date('Y-m-d')) }}">
                            @error('fecha_emision')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>CF</label>
                            <input type="text" name="cf"
                                   class="form-control @error('cf') is-invalid @enderror"
                                   value="{{ old('cf', '14') }}" maxlength="10">
                            @error('cf')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>ST <span class="text-danger">*</span></label>
                            <select name="st" class="form-control @error('st') is-invalid @enderror" required>
                                <option value="">—</option>
                                <option value="22" @selected(old('st') === '22')>22</option>
                                <option value="29" @selected(old('st') === '29')>29</option>
                                <option value="31" @selected(old('st') === '31')>31</option>
                            </select>
                            @error('st')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Tipo gasto <span class="text-danger">*</span></label>
                            <select name="tipo_gasto" id="modal-tipo-gasto"
                                    class="form-control @error('tipo_gasto') is-invalid @enderror" required>
                                <option value="GO"  @selected(old('tipo_gasto', 'GO') === 'GO')>Gasto Operativo</option>
                                <option value="INI" @selected(old('tipo_gasto') === 'INI')>Iniciativa</option>
                            </select>
                            @error('tipo_gasto')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Fila 2: Cuenta presupuestaria | Denominación --}}
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label>Cuenta presupuestaria <span class="text-danger">*</span></label>
                            <select name="cuenta_presupuestaria" id="modal-cuenta-select"
                                    class="form-control @error('cuenta_presupuestaria') is-invalid @enderror" required>
                                <option value="">— Seleccione catálogo —</option>
                                @foreach($catalogos as $cat)
                                    <option value="{{ $cat->catalogo }}"
                                            data-nombre="{{ $cat->nombre }}"
                                            @selected(old('cuenta_presupuestaria') === $cat->catalogo)>
                                        {{ $cat->catalogo }} — {{ $cat->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cuenta_presupuestaria')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-7 mb-3">
                            <label>Denominación <span class="text-danger">*</span></label>
                            <input type="text" name="denominacion" id="modal-denominacion"
                                   class="form-control bg-light @error('denominacion') is-invalid @enderror"
                                   readonly value="{{ old('denominacion') }}"
                                   placeholder="Se completa al seleccionar la cuenta">
                            <small class="text-muted">Se llena automáticamente al seleccionar la cuenta.</small>
                            @error('denominacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Fila 3: Nombre iniciativa | Código iniciativa (solo INI) --}}
                    <div id="modal-seccion-iniciativa"
                         style="{{ old('tipo_gasto') === 'INI' ? '' : 'display:none' }}">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label>Nombre iniciativa</label>
                                <textarea name="nombre_iniciativa" rows="2"
                                          class="form-control @error('nombre_iniciativa') is-invalid @enderror"
                                          placeholder="Descripción de la iniciativa">{{ old('nombre_iniciativa') }}</textarea>
                                @error('nombre_iniciativa')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Código iniciativa</label>
                                <input type="text" name="codigo_iniciativa"
                                       class="form-control @error('codigo_iniciativa') is-invalid @enderror"
                                       value="{{ old('codigo_iniciativa') }}">
                                @error('codigo_iniciativa')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Fila 4: Unidad requirente | Unidad ejecutora | N° UE | Validez | Carácter gasto --}}
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label>Unidad requirente <span class="text-danger">*</span></label>
                            <select name="ccosto_requirente" id="modal-ccosto-requirente"
                                    class="form-control @error('ccosto_requirente') is-invalid @enderror" required>
                                <option value="">— Seleccione unidad —</option>
                                @foreach($ccostos as $cc)
                                    <option value="{{ $cc->ccosto }}"
                                            @selected(old('ccosto_requirente') === $cc->ccosto)>
                                        {{ $cc->ccosto }} — {{ $cc->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ccosto_requirente')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Unidad ejecutora</label>
                            <input type="text" name="unidad_ejecutora"
                                   class="form-control @error('unidad_ejecutora') is-invalid @enderror"
                                   value="{{ old('unidad_ejecutora', 'COYHAIQUE') }}">
                            @error('unidad_ejecutora')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-1 mb-3">
                            <label>N° UE</label>
                            <input type="text" name="numero_ue"
                                   class="form-control @error('numero_ue') is-invalid @enderror"
                                   value="{{ old('numero_ue', '14') }}">
                            @error('numero_ue')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label>Validez <span class="text-danger">*</span></label>
                            <input type="date" name="validez"
                                   class="form-control @error('validez') is-invalid @enderror"
                                   value="{{ old('validez', '2026-12-31') }}" required readonly>
                            <small class="text-muted">Fijada al 31-12-2026.</small>
                            @error('validez')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Carácter gasto <span class="text-danger">*</span></label>
                            <select name="caracter_gasto"
                                    class="form-control @error('caracter_gasto') is-invalid @enderror" required>
                                <option value="">— Seleccione —</option>
                                <option value="TRANSITORIO" @selected(old('caracter_gasto', 'TRANSITORIO') === 'TRANSITORIO')>Transitorio</option>
                                <option value="PERMANENTE"  @selected(old('caracter_gasto') === 'PERMANENTE')>Permanente</option>
                            </select>
                            @error('caracter_gasto')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Fila 5: Monto | Moneda compra | Fecha paridad | Valor paridad --}}
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label>Monto imp. incluido <span class="text-danger">*</span></label>
                            <input type="number" name="monto_impto_incluido" id="modal-monto" step="1" min="0"
                                   class="form-control @error('monto_impto_incluido') is-invalid @enderror"
                                   value="{{ old('monto_impto_incluido') }}" required>
                            @error('monto_impto_incluido')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Moneda compra</label>
                            <select name="moneda_compra" id="modal-moneda-compra"
                                    class="form-control @error('moneda_compra') is-invalid @enderror">
                                <option value="">— Seleccione —</option>
                                <option value="CLP"   @selected(old('moneda_compra') === 'CLP')>CLP — Peso chileno</option>
                                <option value="UF"    @selected(old('moneda_compra') === 'UF')>UF — Unidad de Fomento</option>
                                <option value="Dolar" @selected(old('moneda_compra') === 'Dolar')>Dólar</option>
                            </select>
                            @error('moneda_compra')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3 mb-3" id="modal-grupo-fecha-paridad" style="{{ old('moneda_compra') === 'UF' ? '' : 'display:none' }}">
                            <label>Fecha paridad UF</label>
                            <input type="date" name="fecha_paridad" id="modal-fecha-paridad"
                                   class="form-control @error('fecha_paridad') is-invalid @enderror"
                                   value="{{ old('fecha_paridad', date('Y-m-d')) }}">
                            @error('fecha_paridad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Valor paridad</label>
                            <input type="number" name="valor_paridad" id="modal-valor-paridad" step="0.0001" min="0"
                                   class="form-control bg-light @error('valor_paridad') is-invalid @enderror"
                                   readonly value="{{ old('valor_paridad', old('moneda_compra') === 'CLP' ? '1' : '') }}"
                                   placeholder="Auto">
                            <div id="modal-paridad-mensaje" class="text-danger small mt-1" style="display:none"></div>
                            @error('valor_paridad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Fila 6: Total moneda compra | Medio solicitud --}}
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Total moneda compra</label>
                            <input type="number" name="total_moneda_compra" id="modal-total-moneda" step="0.0001" min="0"
                                   class="form-control bg-light @error('total_moneda_compra') is-invalid @enderror"
                                   readonly value="{{ old('total_moneda_compra') }}" placeholder="Calculado automáticamente">
                            @error('total_moneda_compra')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Medio solicitud</label>
                            <input type="text" name="medio_solicitud"
                                   class="form-control @error('medio_solicitud') is-invalid @enderror"
                                   value="{{ old('medio_solicitud', 'Requerimiento') }}">
                            @error('medio_solicitud')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Generar borrador CDP</button>
                </div>
            </form>

        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Número CDP: pre-llenar al abrir el modal ──────────────────────────
    $('#modal-generar-borrador').on('show.bs.modal', function () {
        const numeroCdpInput = document.getElementById('modal-numero-cdp');
        if (!numeroCdpInput || numeroCdpInput.value) return; // ya tiene valor (old() tras error)

        fetch('{{ route('mesa-ayuda.api.proximo-numero-cdp') }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => { if (data.numero) numeroCdpInput.value = data.numero; })
        .catch(() => { numeroCdpInput.placeholder = 'Error al obtener número'; });
    });

    // ── Denominación auto-fill ────────────────────────────────────────────
    const cuentaSelect      = document.getElementById('modal-cuenta-select');
    const denominacionInput = document.getElementById('modal-denominacion');

    if (cuentaSelect && denominacionInput) {
        cuentaSelect.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            denominacionInput.value = opt.value ? (opt.dataset.nombre ?? '') : '';
        });
    }

    // ── Filtro catálogo por ST ────────────────────────────────────────────
    const stSelect = document.querySelector('[name="st"]');

    function filtrarCatalogoPorSt(st) {
        if (!cuentaSelect) return;
        const opciones = cuentaSelect.querySelectorAll('option');
        opciones.forEach(opt => {
            if (!opt.value) return; // preservar placeholder
            opt.hidden = st ? !opt.value.startsWith(st) : false;
        });
        // Si la selección actual ya no coincide, limpiar
        if (st && cuentaSelect.value && !cuentaSelect.value.startsWith(st)) {
            cuentaSelect.value = '';
            if (denominacionInput) denominacionInput.value = '';
        }
    }

    if (stSelect) {
        stSelect.addEventListener('change', function () {
            filtrarCatalogoPorSt(this.value);
        });
        // Aplicar filtro inicial si hay valor por old()
        filtrarCatalogoPorSt(stSelect.value);
    }

    // ── Toggle sección iniciativa ─────────────────────────────────────────
    const tipoGastoSelect   = document.getElementById('modal-tipo-gasto');
    const seccionIniciativa = document.getElementById('modal-seccion-iniciativa');

    function toggleModalIniciativa() {
        if (!tipoGastoSelect || !seccionIniciativa) return;
        seccionIniciativa.style.display = tipoGastoSelect.value === 'INI' ? '' : 'none';
    }
    if (tipoGastoSelect) tipoGastoSelect.addEventListener('change', toggleModalIniciativa);

    // ── Paridad UF ────────────────────────────────────────────────────────
    const monedaSelect     = document.getElementById('modal-moneda-compra');
    const grupoFechaParidad = document.getElementById('modal-grupo-fecha-paridad');
    const fechaParidadInput = document.getElementById('modal-fecha-paridad');
    const valorParidadInput = document.getElementById('modal-valor-paridad');
    const totalMonedaInput  = document.getElementById('modal-total-moneda');
    const montoInput        = document.getElementById('modal-monto');
    const paridadMensaje    = document.getElementById('modal-paridad-mensaje');

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
        paridadMensaje.style.display = 'none';
        paridadMensaje.textContent   = '';
        paridadMensaje.className     = 'small mt-1';
        valorParidadInput.value      = '';
        totalMonedaInput.value       = '';

        if (!fecha) return;

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
                valorParidadInput.value = data.valor;
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
    }

    function aplicarMoneda(moneda) {
        if (moneda === 'CLP') {
            grupoFechaParidad.style.display = 'none';
            fechaParidadInput.value         = '';
            valorParidadInput.value         = '1';
            valorParidadInput.readOnly      = true;
            calcularTotal();
        } else if (moneda === 'UF') {
            grupoFechaParidad.style.display = '';
            valorParidadInput.readOnly      = true;
            // Si ya hay fecha, buscar paridad automáticamente
            if (fechaParidadInput.value) buscarParidadUf(fechaParidadInput.value);
        } else {
            grupoFechaParidad.style.display = 'none';
            fechaParidadInput.value         = '';
            valorParidadInput.value         = '';
            valorParidadInput.readOnly      = false;
            if (totalMonedaInput) totalMonedaInput.readOnly = false;
        }
    }

    if (monedaSelect) {
        monedaSelect.addEventListener('change', function () {
            aplicarMoneda(this.value);
        });
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
        });
    }

    // Estado inicial al cargar (por old() en error de validación)
    aplicarMoneda(monedaSelect?.value ?? '');

    // ── Re-abrir modal si hubo errores ────────────────────────────────────
    @if($errors->any())
        $('#modal-generar-borrador').modal('show');
    @endif
});
</script>
@endsection

@endsection
