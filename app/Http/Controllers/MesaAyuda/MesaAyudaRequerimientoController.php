<?php

namespace App\Http\Controllers\MesaAyuda;

use App\Http\Controllers\Controller;
use App\Models\Catalogo;
use App\Models\Ccosto;
use App\Models\Estado;
use App\Models\MesaAyudaAdjunto;
use App\Models\MesaAyudaRequerimiento;
use App\Services\MesaAyuda\ClasificadorCdpService;
use App\Services\MesaAyuda\CrearExpedientePresupuestarioDesdeRequerimientoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MesaAyudaRequerimientoController extends Controller
{
    public function index(Request $request): View
    {
        $query = MesaAyudaRequerimiento::query()
            ->with(['estado', 'adjuntos'])
            ->latest('id');

        if ($request->filled('clasificacion')) {
            $query->where('clasificacion', $request->string('clasificacion'));
        }

        if ($request->filled('folio')) {
            $query->where('folio', 'like', '%' . $request->string('folio') . '%');
        }

        if ($request->filled('requiere_cdp')) {
            $query->where('requiere_cdp', $request->boolean('requiere_cdp'));
        }

        // Por defecto muestra solo requerimientos activos (no finalizados).
        // Pasar estado='todos' para ver todos.
        $estadoFiltro = $request->input('estado', '');
        if ($estadoFiltro === '' || $estadoFiltro === null) {
            $query->where(function ($q) {
                $q->whereNull('estado_id')
                  ->orWhereHas('estado', fn ($sq) => $sq->where('es_final', false));
            });
        } elseif ($estadoFiltro !== 'todos') {
            $query->where('estado_id', $estadoFiltro);
        }

        $estados = Estado::deTabla('mesa_ayuda_requerimientos')->orderBy('orden')->get();
        $requerimientos = $query->paginate(15)->withQueryString();

        return view('mesa-ayuda.requerimientos.index', compact('requerimientos', 'estados'));
    }

    public function show(MesaAyudaRequerimiento $requerimiento): View
    {
        $requerimiento->load([
            'historial',
            'adjuntos',
            'estado',
            'expedientePresupuestario',
            'cdpBorradores',
        ]);

        $catalogos = Catalogo::where('estado', 'Activo')->orderBy('catalogo')->get();
        $ccostos   = Ccosto::orderBy('nombre')->get();

        return view('mesa-ayuda.requerimientos.show', compact('requerimiento', 'catalogos', 'ccostos'));
    }

    public function reclasificar(
        MesaAyudaRequerimiento $requerimiento,
        ClasificadorCdpService $clasificador
    ): RedirectResponse {
        $resultado = $clasificador->clasificarModelo($requerimiento);

        $requerimiento->update([
            'clasificacion' => $resultado['tipo_requerimiento'] ?? null,
            'requiere_cdp' => (bool) ($resultado['requiere_cdp'] ?? false),
            'confianza_clasificacion' => $resultado['confianza'] ?? null,
            'score_clasificacion' => (int) ($resultado['score'] ?? 0),
            'evidencias_clasificacion' => $resultado['evidencias'] ?? [],
            'destino_flujo' => $resultado['routing']['destino'] ?? null,
            'procesar_automaticamente' => (bool) ($resultado['routing']['procesar_automaticamente'] ?? false),
            'motivo_routing' => $resultado['routing']['motivo'] ?? null,
        ]);

        return back()->with('success', 'Requerimiento reclasificado correctamente.');
    }

    public function verAdjunto(MesaAyudaAdjunto $adjunto): StreamedResponse|Response
    {
        $relativePath = str_replace('storage/app/', '', $adjunto->ruta_local);

        abort_unless(Storage::exists($relativePath), 404);

        return Storage::response($relativePath, $adjunto->nombre_archivo, [
            'Content-Disposition' => 'inline; filename="' . $adjunto->nombre_archivo . '"',
        ]);
    }

    public function crearExpediente(
        MesaAyudaRequerimiento $requerimiento,
        CrearExpedientePresupuestarioDesdeRequerimientoService $service
    ): RedirectResponse {
        $expediente = $service->crear($requerimiento, auth()->user());

        return redirect()
            ->route('mesa-ayuda.requerimientos.show', $requerimiento)
            ->with('success', "Expediente presupuestario {$expediente->correlativo} creado correctamente.");
    }
}
