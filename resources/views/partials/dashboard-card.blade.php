{{--
    Variables esperadas:
    $label   string  — Título de la tarjeta
    $valor   int     — Número a mostrar
    $icon    string  — Nombre del icono Feather
    $desc    string  — Descripción breve
    $route   string  — Nombre de la ruta (Route::has check)
    $params  array   — Query params para la ruta
    $color   string  — Color hex del icono y acento
--}}
<div class="card border-0 h-100"
     style="border-top: 3px solid {{ $color }} !important; box-shadow: 0 1px 4px rgba(0,0,0,.07);">
    <div class="card-body px-3 py-3">

        <div class="d-flex align-items-start justify-content-between gap-2">

            <div style="flex:1; min-width:0">
                <div class="text-muted mb-1" style="font-size:.72rem; line-height:1.3; font-weight:500; text-transform:uppercase; letter-spacing:.04em">
                    {{ $label }}
                </div>
                <div class="fw-bold lh-1 mb-1" style="font-size:1.9rem; color:#1a2332">
                    {{ number_format($valor) }}
                </div>
                <div class="text-muted" style="font-size:.72rem; line-height:1.3">
                    {{ $desc }}
                </div>
            </div>

            <div class="flex-shrink-0 d-flex align-items-center justify-content-center"
                 style="width:36px; height:36px; background: {{ $color }}18; border-radius:8px; margin-top:2px">
                <i data-feather="{{ $icon }}"
                   style="width:17px; height:17px; color:{{ $color }}; stroke-width:2"></i>
            </div>

        </div>

        @if(Route::has($route))
            <div class="mt-2 pt-2" style="border-top:1px solid #f1f5f9">
                <a href="{{ route($route, $params) }}"
                   class="text-decoration-none"
                   style="font-size:.7rem; color:{{ $color }}; font-weight:500">
                    Ver detalle &rsaquo;
                </a>
            </div>
        @endif

    </div>
</div>
