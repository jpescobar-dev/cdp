<?php

namespace App\Http\Controllers\Contractual;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contractual\StoreDocumentoRevisionContractualRequest;
use App\Models\DocumentoRevisionContractual;
use App\Models\RevisionContractual;
use App\Services\Contractual\PdfTextExtractorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DocumentoRevisionContractualController extends Controller
{
    public function index(RevisionContractual $revision): View
    {
        $revision->load(['documentos.usuario', 'estado', 'usuario']);

        return view('contractual.documentos.index', compact('revision'));
    }

    public function store(
        StoreDocumentoRevisionContractualRequest $request,
        RevisionContractual $revision,
        PdfTextExtractorService $extractor
    ): RedirectResponse {
        $archivo = $request->file('archivo');

        $nombreOriginal = $archivo->getClientOriginalName();
        $extension = strtolower($archivo->getClientOriginalExtension());
        $mimeType = $archivo->getMimeType();
        $tamano = $archivo->getSize();

        $hashArchivo = hash_file('sha256', $archivo->getRealPath());

        $documentoExistente = DocumentoRevisionContractual::query()
            ->where('revision_contractual_id', $revision->id)
            ->where('hash_archivo', $hashArchivo)
            ->first();

        if ($documentoExistente) {
            return redirect()
                ->route('contractual.revisiones.show', $revision)
                ->with('error', 'El documento ya fue cargado previamente en esta revisión.');
        }

        $nombreArchivo = now()->format('YmdHis') . '_' . Str::uuid() . '.' . $extension;

        $ruta = $archivo->storeAs(
            'contractual/revisiones/' . $revision->id,
            $nombreArchivo,
            'public'
        );

        $textoExtraido = null;
        $extraccionEstado = 'PENDIENTE';
        $tieneTextoExtraible = false;

        if ($extension === 'pdf') {
            $resultadoExtraccion = $extractor->extractFromPublicPath($ruta);
            $textoExtraido = $resultadoExtraccion['texto'];
            $extraccionEstado = $resultadoExtraccion['estado'];
            $tieneTextoExtraible = $resultadoExtraccion['tiene_texto_extraible'];
        }

        DocumentoRevisionContractual::create([
            'revision_contractual_id' => $revision->id,
            'nombre_original' => $nombreOriginal,
            'nombre_archivo' => $nombreArchivo,
            'ruta' => $ruta,
            'mime_type' => $mimeType,
            'tamano' => $tamano,
            'extension' => $extension,
            'tipo_documento' => $request->tipo_documento,
            'hash_archivo' => $hashArchivo,
            'texto_extraido' => $textoExtraido,
            'extraccion_estado' => $extraccionEstado,
            'tiene_texto_extraible' => $tieneTextoExtraible,
            'es_vigente' => true,
            'user_id' => auth()->id(),
        ]);

        return redirect()
            ->route('contractual.revisiones.show', $revision)
            ->with('success', 'Documento cargado correctamente.');
    }

    public function destroy(RevisionContractual $revision, DocumentoRevisionContractual $documento): RedirectResponse
    {
        if ((int) $documento->revision_contractual_id !== (int) $revision->id) {
            abort(404);
        }

        if (!empty($documento->ruta) && Storage::disk('public')->exists($documento->ruta)) {
            Storage::disk('public')->delete($documento->ruta);
        }

        $documento->delete();

        return redirect()
            ->route('contractual.revisiones.show', $revision)
            ->with('success', 'Documento eliminado correctamente.');
    }
}
