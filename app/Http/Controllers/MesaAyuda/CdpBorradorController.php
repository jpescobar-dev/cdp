<?php

namespace App\Http\Controllers\MesaAyuda;

use App\Http\Controllers\Controller;
use App\Models\Catalogo;
use App\Models\Ccosto;
use App\Models\CdpBorrador;
use App\Models\MesaAyudaRequerimiento;
use App\Models\ParidadUf;
use App\Services\MesaAyuda\GenerarBorradorCdpService;
use App\Services\Paridad\ObtenerUfCmfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CdpBorradorController extends Controller
{
    public function index(Request $request): View
    {
        $query = CdpBorrador::query()
            ->with(['requerimientoMesaAyuda'])
            ->latest();

        if ($request->filled('estado')) {
            $query->where('estado', $request->string('estado'));
        }

        if ($request->filled('folio')) {
            $query->where('numero_requerimiento', 'like', '%' . $request->string('folio') . '%');
        }

        $borradores = $query->paginate(15)->appends($request->query());

        return view('mesa-ayuda.cdp-borradores.index', compact('borradores'));
    }

    public function apiProximoNumeroCdp(): JsonResponse
    {
        return response()->json(['numero' => CdpBorrador::siguienteNumeroCdp()]);
    }

    public function apiParidadUf(Request $request, ObtenerUfCmfService $cmf): JsonResponse
    {
        $request->validate(['fecha' => ['required', 'date']]);
        $fecha = $request->input('fecha');

        $paridad = ParidadUf::where('fecha', $fecha)->first();

        if ($paridad) {
            return response()->json(['valor' => (float) $paridad->valor]);
        }

        // No está en DB → consultar API externa (CMF / mindicador.cl)
        $resultado = $cmf->obtenerYGuardar($fecha);

        if ($resultado !== null) {
            return response()->json([
                'valor'      => $resultado['valor'],
                'advertencia' => "Valor UF obtenido desde {$resultado['fuente']} y registrado en el sistema.",
            ]);
        }

        return response()->json([
            'error' => "No existe valor UF para el {$fecha} en el sistema ni en las APIs externas. Actualice la tabla de paridad UF manualmente.",
        ], 404);
    }

    public function generarDesdeRequerimiento(
        Request $request,
        MesaAyudaRequerimiento $requerimiento,
        GenerarBorradorCdpService $service
    ): RedirectResponse {
        $existente = CdpBorrador::where('mesa_ayuda_requerimiento_id', $requerimiento->id)->first();
        if ($existente) {
            return redirect()->route('mesa-ayuda.cdp-borradores.show', $existente);
        }

        $data = $request->validate([
            'numero_cdp'            => ['nullable', 'string', 'max:100', 'unique:cdp_borradores,numero_cdp'],
            'fecha_emision'         => ['nullable', 'date'],
            'cf'                    => ['nullable', 'string', 'max:10'],
            'st'                    => ['required', Rule::in(['22', '29', '31'])],
            'tipo_gasto'            => ['required', Rule::in(['GO', 'INI'])],
            'cuenta_presupuestaria' => ['required', 'string', 'exists:catalogos,catalogo'],
            'denominacion'          => ['required', 'string'],
            'nombre_iniciativa'     => ['nullable', 'string'],
            'codigo_iniciativa'     => ['nullable', 'string', 'max:100'],
            'unidad_ejecutora'      => ['nullable', 'string', 'max:255'],
            'numero_ue'             => ['nullable', 'string', 'max:20'],
            'validez'               => ['required', 'date'],
            'caracter_gasto'        => ['required', Rule::in(['TRANSITORIO', 'PERMANENTE'])],
            'monto_impto_incluido'  => ['nullable', 'numeric', 'min:0'],
            'moneda_compra'         => ['nullable', Rule::in(['CLP', 'UF', 'Dolar'])],
            'fecha_paridad'         => ['nullable', 'date'],
            'valor_paridad'         => ['nullable', 'numeric', 'min:0'],
            'total_moneda_compra'   => ['nullable', 'numeric', 'min:0'],
            'medio_solicitud'       => ['nullable', 'string', 'max:100'],
            'ccosto_requirente'     => ['nullable', 'string', 'exists:ccostos,ccosto'],
        ], [
            'numero_cdp.unique'              => 'Este número CDP ya está en uso.',
            'st.required'                    => 'Debe seleccionar un subtítulo (ST).',
            'st.in'                          => 'El ST debe ser 22, 29 o 31.',
            'cuenta_presupuestaria.required' => 'Debe seleccionar una cuenta presupuestaria.',
            'cuenta_presupuestaria.exists'   => 'La cuenta seleccionada no existe en el catálogo.',
            'denominacion.required'          => 'La denominación es obligatoria.',
            'validez.required'               => 'La fecha de validez es obligatoria.',
            'validez.date'                   => 'Formato de fecha inválido.',
            'caracter_gasto.required'        => 'Debe seleccionar el carácter del gasto.',
            'caracter_gasto.in'              => 'El carácter debe ser Transitorio o Permanente.',
        ]);

        $borrador = $service->generarDesdeRequerimiento($requerimiento, $data);

        return redirect()
            ->route('mesa-ayuda.cdp-borradores.show', $borrador)
            ->with('success', 'Borrador CDP generado correctamente.');
    }

    public function show(CdpBorrador $borrador): View
    {
        $borrador->load(['requerimientoMesaAyuda.adjuntos', 'expedientePresupuestario']);
        $catalogos = Catalogo::where('estado', 'Activo')->orderBy('catalogo')->get();
        $ccostos   = Ccosto::orderBy('nombre')->get();

        return view('mesa-ayuda.cdp-borradores.show', compact('borrador', 'catalogos', 'ccostos'));
    }

    public function update(Request $request, CdpBorrador $borrador): RedirectResponse
    {
        $data = $request->validate([
            'numero_cdp'                  => ['required', 'string', 'max:100', Rule::unique('cdp_borradores', 'numero_cdp')->ignore($borrador->id)],
            'fecha_emision'               => ['nullable', 'date'],
            'cf'                          => ['nullable', 'string', 'max:10'],
            'st'                          => ['required', Rule::in(['22', '29', '31'])],
            'tipo_gasto'                  => ['required', Rule::in(['GO', 'INI'])],
            'nombre_iniciativa'           => ['nullable', 'string'],
            'codigo_iniciativa'           => ['nullable', 'string', 'max:100'],
            'cuenta_presupuestaria'       => ['required', 'string', 'exists:catalogos,catalogo'],
            'denominacion'                => ['required', 'string'],
            'unidad_ejecutora'            => ['nullable', 'string', 'max:255'],
            'numero_ue'                   => ['nullable', 'string', 'max:20'],
            'monto_impto_incluido'        => ['nullable', 'numeric', 'min:0'],
            'validez'                     => ['required', 'date'],
            'caracter_gasto'              => ['required', Rule::in(['TRANSITORIO', 'PERMANENTE'])],
            'medio_solicitud'             => ['nullable', 'string', 'max:100'],
            'numero_requerimiento'        => ['nullable', 'string', 'max:50'],
            'moneda_compra'               => ['nullable', Rule::in(['CLP', 'UF', 'Dolar'])],
            'fecha_paridad'               => ['nullable', 'date'],
            'valor_paridad'               => ['nullable', 'numeric', 'min:0'],
            'total_moneda_compra'         => ['nullable', 'numeric', 'min:0'],
            'ccosto_requirente'           => ['nullable', 'string', 'exists:ccostos,ccosto'],
            'texto_certificacion'         => ['nullable', 'string'],
            'respuesta_mesa_ayuda_borrador' => ['nullable', 'string'],
        ], [
            'numero_cdp.required'            => 'El número CDP es obligatorio.',
            'numero_cdp.unique'              => 'Este número CDP ya está asignado a otro borrador.',
            'st.required'                    => 'Debe seleccionar un subtítulo (ST).',
            'st.in'                          => 'El ST debe ser 22, 29 o 31.',
            'cuenta_presupuestaria.required' => 'Debe seleccionar una cuenta presupuestaria.',
            'cuenta_presupuestaria.exists'   => 'La cuenta presupuestaria seleccionada no existe en el catálogo.',
            'denominacion.required'          => 'La denominación es obligatoria.',
            'validez.required'               => 'La fecha de validez es obligatoria.',
            'validez.date'                   => 'La fecha de validez no tiene un formato válido.',
            'caracter_gasto.required'        => 'Debe seleccionar el carácter del gasto.',
            'caracter_gasto.in'              => 'El carácter del gasto debe ser Transitorio o Permanente.',
        ]);

        $borrador->update($data + [
            'estado'          => $borrador->estado === 'aprobado' ? 'aprobado' : 'borrador_editado',
            'datos_faltantes' => CdpBorrador::calcularDatosFaltantes(
                array_merge($borrador->toArray(), $data)
            ),
        ]);

        return back()->with('success', 'Borrador actualizado correctamente.');
    }

    public function aprobar(Request $request, CdpBorrador $borrador): RedirectResponse
    {
        $request->validate([
            'confirma_disponibilidad' => ['required', 'accepted'],
            'observacion_aprobacion'  => ['nullable', 'string', 'max:5000'],
        ], [
            'confirma_disponibilidad.required' => 'Debe confirmar la disponibilidad presupuestaria para aprobar.',
            'confirma_disponibilidad.accepted'  => 'Debe confirmar la disponibilidad presupuestaria para aprobar.',
        ]);

        $usuario  = auth()->user()?->name ?? auth()->id();
        $ahora    = now()->toISOString();

        $eventos = $borrador->advertencias ?? [];
        $eventos[] = [
            'tipo'    => 'aprobacion',
            'texto'   => 'Borrador aprobado. El autorizador certificó la existencia de disponibilidad presupuestaria.',
            'usuario' => $usuario,
            'fecha'   => $ahora,
        ];

        if ($request->filled('observacion_aprobacion')) {
            $eventos[] = [
                'tipo'    => 'observacion_usuario',
                'texto'   => $request->input('observacion_aprobacion'),
                'usuario' => $usuario,
                'fecha'   => $ahora,
            ];
        }

        $borrador->update([
            'estado'               => 'aprobado',
            'aprobado_por_usuario' => true,
            'aprobado_por'         => auth()->user()?->rut,
            'fecha_aprobacion'     => now(),
            'datos_faltantes'      => [],
            'advertencias'         => $eventos,
        ]);

        return back()->with('success', 'Borrador aprobado correctamente.');
    }

    public function rechazar(Request $request, CdpBorrador $borrador): RedirectResponse
    {
        $data = $request->validate([
            'motivo' => ['required', 'string', 'max:5000'],
        ]);

        $eventos = $borrador->advertencias ?? [];
        $eventos[] = [
            'tipo'    => 'rechazo',
            'texto'   => $data['motivo'],
            'usuario' => auth()->user()?->name ?? auth()->id(),
            'fecha'   => now()->toISOString(),
        ];

        $borrador->update([
            'estado'             => 'rechazado',
            'aprobado_por_usuario' => false,
            'advertencias'       => $eventos,
        ]);

        return back()->with('error', 'Borrador rechazado. Corrija los datos y vuelva a aprobar.');
    }

    public function observar(Request $request, CdpBorrador $borrador): RedirectResponse
    {
        $data = $request->validate([
            'observacion' => ['required', 'string', 'max:5000'],
        ]);

        $eventos = $borrador->advertencias ?? [];
        $eventos[] = [
            'tipo'    => 'observacion_usuario',
            'texto'   => $data['observacion'],
            'usuario' => auth()->user()?->name ?? auth()->id(),
            'fecha'   => now()->toISOString(),
        ];

        $borrador->update([
            'estado'      => 'observado_usuario',
            'advertencias' => $eventos,
        ]);

        return back()->with('success', 'Observación registrada correctamente.');
    }
}
