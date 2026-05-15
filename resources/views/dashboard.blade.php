@extends('layouts.theme.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid py-4" style="max-width:1100px">

    {{-- Encabezado institucional --}}
    <div class="d-flex justify-content-between align-items-end mb-4 pb-3"
         style="border-bottom:2px solid #e2e8f0">
        <div>
            <div class="text-muted"
                 style="font-size:.68rem;letter-spacing:.09em;text-transform:uppercase;font-weight:600">
                Corporación Administrativa del Poder Judicial
            </div>
            <h1 class="mb-0 fw-semibold" style="font-size:1.15rem;color:#1a2332">
                Certificados de Disponibilidad Presupuestaria
            </h1>
        </div>
        <div class="text-end text-muted" style="font-size:.73rem">
            <div>{{ now()->isoFormat('dddd') }}</div>
            <div class="fw-semibold" style="color:#334155">{{ now()->format('d \d\e F \d\e Y') }}</div>
        </div>
    </div>

    @php
        $etapas = [
            [
                'numero' => '1',
                'titulo' => 'Solicitudes y Captura',
                'color'  => '#1e40af',
                'items'  => [
                    [
                        'icon'     => 'file-plus',
                        'label'    => 'Solicitudes CDP',
                        'valor'    => $solicitudesTotal,
                        'desc_ok'  => 'Formularios completados',
                        'desc_vacio'=> 'Sin solicitudes aún',
                        'alerta'   => false,
                        'route'    => 'cdp.solicitudes.index',
                        'params'   => [],
                    ],
                    [
                        'icon'     => 'download',
                        'label'    => 'PDF generados',
                        'valor'    => $solicitudesPdfGenerado,
                        'desc_ok'  => 'Listos para adjuntar',
                        'desc_vacio'=> 'Sin PDF generados',
                        'alerta'   => false,
                        'route'    => 'cdp.solicitudes.index',
                        'params'   => [],
                    ],
                    [
                        'icon'     => 'inbox',
                        'label'    => 'Requerimientos CDP',
                        'valor'    => $requerimientosCdp,
                        'desc_ok'  => 'Capturados de Mesa de Ayuda',
                        'desc_vacio'=> 'Sin requerimientos capturados',
                        'alerta'   => false,
                        'route'    => 'mesa-ayuda.requerimientos.index',
                        'params'   => ['requiere_cdp' => '1'],
                    ],
                    [
                        'icon'     => 'alert-triangle',
                        'label'    => 'Sin proceso iniciado',
                        'valor'    => $requerimientosSinExpediente,
                        'desc_ok'  => 'Requieren crear expediente',
                        'desc_vacio'=> 'Todos con expediente',
                        'alerta'   => true,
                        'route'    => 'mesa-ayuda.requerimientos.index',
                        'params'   => ['requiere_cdp' => '1'],
                    ],
                ],
            ],
            [
                'numero' => '2',
                'titulo' => 'Borradores CDP',
                'color'  => '#7c3aed',
                'items'  => [
                    [
                        'icon'     => 'edit-3',
                        'label'    => 'Pendientes de aprobar',
                        'valor'    => $borradorPendiente,
                        'desc_ok'  => 'Esperan revisión del analista',
                        'desc_vacio'=> 'Sin borradores pendientes',
                        'alerta'   => true,
                        'route'    => 'mesa-ayuda.cdp-borradores.index',
                        'params'   => [],
                    ],
                    [
                        'icon'     => 'check-circle',
                        'label'    => 'Aprobados',
                        'valor'    => $borradorAprobado,
                        'desc_ok'  => 'Revisados y aprobados',
                        'desc_vacio'=> 'Sin borradores aprobados',
                        'alerta'   => false,
                        'route'    => 'mesa-ayuda.cdp-borradores.index',
                        'params'   => ['estado' => 'aprobado'],
                    ],
                ],
            ],
            [
                'numero' => '3',
                'titulo' => 'Expedientes Presupuestarios',
                'color'  => '#065f46',
                'items'  => $estadosExp->map(function($estado, $i) use ($expedientesPorEstado) {
                    $iconos   = ['folder-plus', 'search', 'check-square', 'printer'];
                    $desc_ok  = ['Expedientes en revisión pendiente', 'En análisis presupuestario', 'Aprobados para emisión', 'CDP emitidos'];
                    return [
                        'icon'      => $iconos[$i] ?? 'folder',
                        'label'     => $estado->nombre,
                        'valor'     => $expedientesPorEstado[$estado->id] ?? 0,
                        'desc_ok'   => $desc_ok[$i] ?? 'Expedientes en este estado',
                        'desc_vacio'=> 'Sin expedientes en este estado',
                        'alerta'    => false,
                        'route'     => 'presupuesto.expedientes.index',
                        'params'    => [],
                    ];
                })->values()->toArray(),
            ],
        ];
    @endphp

    {{-- LÍNEA DE TIEMPO --}}
    <div class="position-relative" style="padding-left:3rem">

        {{-- Eje vertical --}}
        <div style="position:absolute;left:11px;top:14px;bottom:30px;width:2px;
                    background:linear-gradient(to bottom,#1e40af 0%,#7c3aed 50%,#065f46 100%);
                    opacity:.2;border-radius:2px"></div>

        @foreach($etapas as $etapa)

            <div class="position-relative mb-5">

                {{-- Dot numerado de la etapa --}}
                <div style="position:absolute;left:-3rem;top:2px;width:24px;height:24px;
                            border-radius:50%;background:{{ $etapa['color'] }};
                            display:flex;align-items:center;justify-content:center;
                            box-shadow:0 0 0 5px {{ $etapa['color'] }}1a">
                    <span style="color:#fff;font-size:.6rem;font-weight:800">{{ $etapa['numero'] }}</span>
                </div>

                {{-- Título etapa --}}
                <div class="mb-3 d-flex align-items-center gap-2">
                    <span style="font-size:.67rem;font-weight:700;letter-spacing:.09em;
                                 text-transform:uppercase;color:{{ $etapa['color'] }}">
                        Etapa {{ $etapa['numero'] }}
                    </span>
                    <span style="font-size:.88rem;font-weight:600;color:#1a2332">
                        — {{ $etapa['titulo'] }}
                    </span>
                </div>

                {{-- Ítems en línea horizontal --}}
                <div class="position-relative">

                    {{-- Línea conectora horizontal --}}
                    <div style="position:absolute;top:24px;left:28px;right:28px;
                                height:2px;background:{{ $etapa['color'] }};
                                opacity:.1;z-index:0"></div>

                    <div class="row g-2">
                        @foreach($etapa['items'] as $item)
                            @php
                                $activo   = $item['valor'] > 0;
                                $esAlerta = $item['alerta'] && $activo;

                                // Estado visual del nodo
                                if ($esAlerta) {
                                    $bgCirculo  = '#fff';
                                    $border     = "3px solid {$etapa['color']}";
                                    $iconColor  = $etapa['color'];
                                    $sombra     = "0 0 0 5px {$etapa['color']}22, 0 2px 8px {$etapa['color']}33";
                                    $numColor   = $etapa['color'];
                                    $tagBg      = '#fff3cd';
                                    $tagColor   = '#92400e';
                                    $tagTexto   = 'Requiere atención';
                                    $tagIcon    = '⚠';
                                } elseif ($activo) {
                                    $bgCirculo  = $etapa['color'];
                                    $border     = "3px solid {$etapa['color']}";
                                    $iconColor  = '#fff';
                                    $sombra     = "0 4px 12px {$etapa['color']}44";
                                    $numColor   = '#1a2332';
                                    $tagBg      = $etapa['color'] . '15';
                                    $tagColor   = $etapa['color'];
                                    $tagTexto   = $item['desc_ok'];
                                    $tagIcon    = '●';
                                } else {
                                    $bgCirculo  = '#f1f5f9';
                                    $border     = '3px solid #cbd5e1';
                                    $iconColor  = '#64748b';
                                    $sombra     = 'none';
                                    $numColor   = '#94a3b8';
                                    $tagBg      = '#f1f5f9';
                                    $tagColor   = '#94a3b8';
                                    $tagTexto   = $item['desc_vacio'];
                                    $tagIcon    = '—';
                                }
                            @endphp

                            <div class="col-6 col-md-3 col-lg" style="z-index:1">
                                <div class="text-center px-1 pt-1 pb-3">

                                    {{-- Ícono circular --}}
                                    <div class="mx-auto mb-2 d-flex align-items-center justify-content-center"
                                         style="width:52px;height:52px;
                                                border-radius:50%;
                                                background:{{ $bgCirculo }};
                                                border:{{ $border }};
                                                box-shadow:{{ $sombra }};
                                                color:{{ $iconColor }};
                                                transition:all .2s">
                                        <i data-feather="{{ $item['icon'] }}"
                                           style="width:22px;height:22px"></i>
                                    </div>

                                    {{-- Número --}}
                                    <div class="fw-bold lh-1 mb-1"
                                         style="font-size:1.75rem;color:{{ $numColor }}">
                                        {{ number_format($item['valor']) }}
                                    </div>

                                    {{-- Etiqueta --}}
                                    <div style="font-size:.67rem;font-weight:700;
                                                text-transform:uppercase;letter-spacing:.04em;
                                                color:#475569;line-height:1.3;margin-bottom:.35rem">
                                        {{ $item['label'] }}
                                    </div>

                                    {{-- Píldora de estado --}}
                                    <div class="d-inline-flex align-items-center gap-1 rounded-pill px-2 py-1 mb-2"
                                         style="background:{{ $tagBg }};font-size:.62rem;
                                                font-weight:600;color:{{ $tagColor }};
                                                line-height:1.2;max-width:100%">
                                        <span>{{ $tagIcon }}</span>
                                        <span class="text-truncate" style="max-width:110px">{{ $tagTexto }}</span>
                                    </div>

                                    {{-- Enlace --}}
                                    @if(Route::has($item['route']))
                                        <div>
                                            <a href="{{ route($item['route'], $item['params']) }}"
                                               class="text-decoration-none"
                                               style="font-size:.65rem;font-weight:600;
                                                      color:{{ $activo ? $etapa['color'] : '#94a3b8' }}">
                                                Ver detalle &rsaquo;
                                            </a>
                                        </div>
                                    @endif

                                </div>
                            </div>

                        @endforeach
                    </div>

                </div>
            </div>

        @endforeach

        {{-- Nodo final: CDP Emitido --}}
        <div class="position-relative">
            <div style="position:absolute;left:-3rem;top:2px;width:24px;height:24px;
                        border-radius:50%;background:#f1f5f9;border:2px solid #cbd5e1;
                        display:flex;align-items:center;justify-content:center;
                        color:#94a3b8">
                <i data-feather="award" style="width:11px;height:11px"></i>
            </div>
            <p class="mb-0 pt-1" style="font-size:.72rem;font-weight:600;color:#94a3b8;
                                        text-transform:uppercase;letter-spacing:.06em">
                CDP Emitido — Proceso completado
            </p>
        </div>

    </div>

</div>
@endsection
