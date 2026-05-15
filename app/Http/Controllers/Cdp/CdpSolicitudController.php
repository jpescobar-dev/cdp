<?php

namespace App\Http\Controllers\Cdp;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cdp\StoreCdpSolicitudRequest;
use App\Models\Ccosto;
use App\Models\CdpSolicitud;
use App\Models\Proyecto;
use App\Services\Cdp\GenerarPdfCdpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CdpSolicitudController extends Controller
{
    public function __construct(
        private readonly GenerarPdfCdpService $pdfService,
    ) {}

    public function index(): View
    {
        $solicitudes = CdpSolicitud::where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('cdp.solicitudes.index', compact('solicitudes'));
    }

    public function create(): View
    {
        $ccostos   = Ccosto::orderBy('ccosto')->get();
        $proyectos = Proyecto::orderBy('proyecto')->get();

        return view('cdp.solicitudes.create', compact('ccostos', 'proyectos'));
    }

    public function store(StoreCdpSolicitudRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $solicitud = CdpSolicitud::create([
            'user_id'           => auth()->id(),
            'nombre_requirente' => $data['nombre_requirente'],
            'rut_requirente'    => $data['rut_requirente'],
            'unidad_requirente' => $data['unidad_requirente'],
            'ccosto'            => $data['ccosto'] ?? null,
            'requerimiento'     => $data['requerimiento'] ?? null,
            'glosa'             => $data['glosa'],
            'proveedor'         => $data['proveedor'],
            'monto_estimado'    => $data['monto_estimado'],
            'moneda'            => $data['moneda'],
            'tipo_gasto1'       => $data['tipo_gasto1'] ?? null,
            'tipo_gasto2'       => $data['tipo_gasto2'] ?? null,
            'proyecto_id'       => $data['proyecto_id'] ?? null,
        ]);

        $rutas = [];
        if ($request->hasFile('documentos')) {
            foreach ($request->file('documentos') as $archivo) {
                $ruta = $archivo->store("cdp/solicitudes/{$solicitud->id}/docs");
                $rutas[] = [
                    'nombre' => $archivo->getClientOriginalName(),
                    'ruta'   => $ruta,
                    'mime'   => $archivo->getClientMimeType(),
                ];
            }
        }

        $solicitud->update(['documentos' => $rutas ?: null]);

        $pdfPath = $this->pdfService->generar($solicitud);
        $solicitud->update(['pdf_path' => $pdfPath, 'estado' => 'pdf_generado']);

        return redirect()
            ->route('cdp.solicitudes.show', $solicitud)
            ->with('success', 'Solicitud registrada. Descargue el PDF y adjúntelo a su requerimiento en Mesa de Ayuda.');
    }

    public function show(CdpSolicitud $solicitud): View
    {
        abort_unless($solicitud->user_id === auth()->id(), 403);

        return view('cdp.solicitudes.show', compact('solicitud'));
    }

    public function descargar(CdpSolicitud $solicitud): StreamedResponse
    {
        abort_unless($solicitud->user_id === auth()->id(), 403);
        abort_if(! $solicitud->pdf_path, 404);

        return response()->streamDownload(function () use ($solicitud) {
            echo \Illuminate\Support\Facades\Storage::get($solicitud->pdf_path);
        }, "{$solicitud->nombreCdp()}.pdf", ['Content-Type' => 'application/pdf']);
    }
}
