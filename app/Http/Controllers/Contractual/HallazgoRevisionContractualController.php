<?php

namespace App\Http\Controllers\Contractual;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contractual\StoreHallazgoRevisionContractualRequest;
use App\Models\Estado;
use App\Models\HallazgoRevisionContractual;
use App\Models\RevisionContractual;
use App\Models\SnapshotRevisionContractual;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HallazgoRevisionContractualController extends Controller
{
    public function index(RevisionContractual $revision, SnapshotRevisionContractual $snapshot): View
    {
        if ((int) $snapshot->revision_contractual_id !== (int) $revision->id) {
            abort(404);
        }

        $snapshot->load(['hallazgos.estado', 'hallazgos.usuario']);
        $revision->load(['estado', 'usuario']);

        return view('contractual.hallazgos.index', compact('revision', 'snapshot'));
    }

    public function create(RevisionContractual $revision, SnapshotRevisionContractual $snapshot): View
    {
        if ((int) $snapshot->revision_contractual_id !== (int) $revision->id) {
            abort(404);
        }

        $estados = Estado::query()
            ->where('tabla_referencia', 'hallazgos_revision')
            ->orderBy('nombre')
            ->get();

        return view('contractual.hallazgos.create', compact('revision', 'snapshot', 'estados'));
    }

    public function store(
        StoreHallazgoRevisionContractualRequest $request,
        RevisionContractual $revision,
        SnapshotRevisionContractual $snapshot
    ): RedirectResponse {
        if ((int) $snapshot->revision_contractual_id !== (int) $revision->id) {
            abort(404);
        }

        HallazgoRevisionContractual::create([
            'snapshot_revision_contractual_id' => $snapshot->id,
            'estado_id' => $request->estado_id,
            'titulo' => $request->titulo,
            'tipo_hallazgo' => $request->tipo_hallazgo,
            'tipo_riesgo' => $request->tipo_riesgo,
            'nivel_criticidad' => $request->nivel_criticidad,
            'hecho_acreditado' => $request->hecho_acreditado,
            'observacion' => $request->observacion,
            'fundamento_documental' => $request->fundamento_documental,
            'consecuencia_posible' => $request->consecuencia_posible,
            'recomendacion' => $request->recomendacion,
            'user_id' => auth()->id(),
        ]);

        return redirect()
            ->route('contractual.revisiones.snapshots.hallazgos.index', [$revision, $snapshot])
            ->with('success', 'Hallazgo registrado correctamente.');
    }

    public function show(
        RevisionContractual $revision,
        SnapshotRevisionContractual $snapshot,
        HallazgoRevisionContractual $hallazgo
    ): View {
        if ((int) $snapshot->revision_contractual_id !== (int) $revision->id) {
            abort(404);
        }

        if ((int) $hallazgo->snapshot_revision_contractual_id !== (int) $snapshot->id) {
            abort(404);
        }

        $hallazgo->load(['estado', 'usuario']);
        $revision->load(['estado', 'usuario']);

        return view('contractual.hallazgos.show', compact('revision', 'snapshot', 'hallazgo'));
    }
}
