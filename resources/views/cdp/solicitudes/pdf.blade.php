<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $solicitud->nombreCdp() }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            color: #1a1a1a;
            line-height: 1.4;
        }

        .header {
            border-bottom: 3px solid #003366;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .header-title {
            font-size: 16pt;
            font-weight: bold;
            color: #003366;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header-sub {
            font-size: 10pt;
            color: #555;
            text-align: center;
            margin-top: 4px;
        }

        .folio-box {
            border: 1px solid #003366;
            padding: 6px 14px;
            display: inline-block;
            margin-top: 8px;
            font-size: 10pt;
            color: #003366;
        }

        .section-title {
            background-color: #003366;
            color: #ffffff;
            font-weight: bold;
            font-size: 10pt;
            padding: 5px 10px;
            margin-bottom: 8px;
            margin-top: 16px;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table.data td {
            padding: 5px 8px;
            border: 1px solid #ccc;
            vertical-align: top;
        }

        table.data td.label {
            background-color: #f0f4f8;
            font-weight: bold;
            width: 35%;
            color: #333;
        }

        .glosa-box {
            border: 1px solid #ccc;
            padding: 10px;
            min-height: 60px;
            white-space: pre-line;
            font-size: 10.5pt;
        }

        .footer {
            margin-top: 40px;
            border-top: 1px solid #ccc;
            padding-top: 12px;
            font-size: 9pt;
            color: #777;
            text-align: center;
        }

        .firma-area {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .firma-bloque {
            width: 45%;
            border-top: 1px solid #333;
            padding-top: 6px;
            font-size: 9.5pt;
            text-align: center;
        }

        .aviso {
            background: #fff8e1;
            border: 1px solid #f0ad00;
            padding: 8px 12px;
            font-size: 9.5pt;
            color: #7a5800;
            margin-top: 16px;
        }
    </style>
</head>
<body>

    {{-- Encabezado institucional --}}
    <div class="header">
        <div class="header-title">Solicitud de Certificado de Disponibilidad Presupuestaria</div>
        <div class="header-sub">Corporación Administrativa del Poder Judicial (CAPJ)</div>
        <div style="text-align:right;">
            <span class="folio-box">{{ $solicitud->nombreCdp() }}</span>
        </div>
    </div>

    {{-- Requirente --}}
    <div class="section-title">1. Datos del Requirente</div>
    <table class="data">
        <tr>
            <td class="label">Nombre completo</td>
            <td>{{ $solicitud->nombre_requirente }}</td>
        </tr>
        <tr>
            <td class="label">RUT</td>
            <td>{{ $solicitud->rut_requirente }}</td>
        </tr>
        <tr>
            <td class="label">Unidad / Departamento</td>
            <td>{{ $solicitud->unidad_requirente }}</td>
        </tr>
        @if($solicitud->ccosto)
        <tr>
            <td class="label">Centro de Costo</td>
            <td>{{ $solicitud->ccosto }}</td>
        </tr>
        @endif
        @if($solicitud->requerimiento)
        <tr>
            <td class="label">N° Requerimiento</td>
            <td>{{ $solicitud->requerimiento }}</td>
        </tr>
        @endif
        <tr>
            <td class="label">Fecha de solicitud</td>
            <td>{{ $solicitud->created_at->format('d/m/Y') }}</td>
        </tr>
    </table>

    {{-- Datos del gasto --}}
    <div class="section-title">2. Datos del Gasto</div>
    <table class="data">
        <tr>
            <td class="label">Proveedor</td>
            <td>{{ $solicitud->proveedor }}</td>
        </tr>
        <tr>
            <td class="label">Monto estimado</td>
            <td>{{ $solicitud->montoFormateado() }}</td>
        </tr>
        <tr>
            <td class="label">Moneda</td>
            <td>{{ $solicitud->moneda }}</td>
        </tr>
        @if($solicitud->tipo_gasto1)
        <tr>
            <td class="label">Tipo de Gasto</td>
            <td>{{ $solicitud->tipo_gasto1 === 'GO' ? 'GO — Gasto Operacional' : 'INI — Inversión' }}</td>
        </tr>
        @endif
        @if($solicitud->tipo_gasto2)
        <tr>
            <td class="label">Clasificación</td>
            <td>{{ ucfirst(strtolower($solicitud->tipo_gasto2)) }}</td>
        </tr>
        @endif
    </table>

    {{-- Glosa --}}
    <div class="section-title">3. Descripción del Gasto (Glosa)</div>
    <div class="glosa-box">{{ $solicitud->glosa }}</div>

    {{-- Documentos adjuntos --}}
    @if($solicitud->documentos)
        <div class="section-title">4. Documentos de Respaldo Adjuntos</div>
        <table class="data">
            @foreach($solicitud->documentos as $i => $doc)
                <tr>
                    <td class="label">Documento {{ $i + 1 }}</td>
                    <td>{{ $doc['nombre'] }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    {{-- Aviso al requirente --}}
    <div class="aviso">
        <strong>Instrucción:</strong> Este documento debe ser adjuntado por el requirente al momento de ingresar
        su requerimiento en la plataforma <strong>Mesa de Ayuda</strong>. El área de Presupuesto procesará
        la certificación de disponibilidad presupuestaria a partir de esta solicitud.
    </div>

    {{-- Firmas --}}
    <table style="width:100%; margin-top:50px;">
        <tr>
            <td style="width:45%; border-top:1px solid #333; padding-top:6px; text-align:center; font-size:9.5pt;">
                <strong>{{ $solicitud->nombre_requirente }}</strong><br>
                {{ $solicitud->unidad_requirente }}<br>
                <span style="color:#777;">Requirente</span>
            </td>
            <td style="width:10%;"></td>
            <td style="width:45%; border-top:1px solid #333; padding-top:6px; text-align:center; font-size:9.5pt;">
                <br>
                <br>
                <span style="color:#777;">Revisado por Presupuesto</span>
            </td>
        </tr>
    </table>

    <div class="footer">
        Generado el {{ now()->format('d/m/Y H:i') }} — Sistema CDP · CAPJ
    </div>

</body>
</html>
