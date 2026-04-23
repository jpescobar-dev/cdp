<?php

namespace App\Http\Controllers\Contractual;

use App\Http\Controllers\Controller;
use App\Models\RevisionContractual;
use App\Models\SnapshotRevisionContractual;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SnapshotRevisionContractualController extends Controller
{
    public function index(RevisionContractual $revision): View
    {
        $revision->load(['snapshots.usuario', 'estado', 'usuario']);

        return view('contractual.snapshots.index', compact('revision'));
    }

    public function store(RevisionContractual $revision): RedirectResponse
    {
        $ultimaVersion = $revision->snapshots()->max('numero_version') ?? 0;
        $nuevaVersion = $ultimaVersion + 1;

        SnapshotRevisionContractual::where('revision_contractual_id', $revision->id)
            ->update(['es_actual' => false]);

        SnapshotRevisionContractual::create([
            'revision_contractual_id' => $revision->id,
            'numero_version' => $nuevaVersion,
            'tipo_ejecucion' => 'manual',
            'resumen' => 'Snapshot manual generado desde la revisión contractual.',
            'json_resultado' => null,
            'es_actual' => true,
            'user_id' => auth()->id(),
        ]);

        return redirect()
            ->route('contractual.revisiones.show', $revision)
            ->with('success', 'Snapshot generado correctamente.');
    }

    public function show(RevisionContractual $revision, SnapshotRevisionContractual $snapshot): View
    {
        if ((int) $snapshot->revision_contractual_id !== (int) $revision->id) {
            abort(404);
        }

        $snapshot->load(['usuario']);
        $revision->load(['estado', 'usuario']);

        return view('contractual.snapshots.show', compact('revision', 'snapshot'));
    }
}