<?php

namespace App\Http\Controllers\Contractual;

use App\Http\Controllers\Controller;
use App\Models\ChecklistRevisionContractual;
use App\Models\RevisionContractual;
use App\Models\SnapshotRevisionContractual;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ChecklistRevisionContractualController extends Controller
{
    public function index(RevisionContractual $revision, SnapshotRevisionContractual $snapshot): View
    {
        if ($snapshot->revision_contractual_id !== $revision->id) {
            abort(404);
        }

        $snapshot->load('checklist');

        return view('contractual.checklist.index', compact('revision', 'snapshot'));
    }

    public function create(RevisionContractual $revision, SnapshotRevisionContractual $snapshot): View
    {
        if ($snapshot->revision_contractual_id !== $revision->id) {
            abort(404);
        }

        return view('contractual.checklist.create', compact('revision', 'snapshot'));
    }

    public function store(Request $request, RevisionContractual $revision, SnapshotRevisionContractual $snapshot): RedirectResponse
    {
        if ($snapshot->revision_contractual_id !== $revision->id) {
            abort(404);
        }

        $validated = $request->validate([
            'item' => ['required', 'string', 'max:255'],
            'estado_item' => ['required', 'string', 'max:50'],
            'observacion' => ['nullable', 'string'],
            'referencia_documental' => ['nullable', 'string'],
            'orden' => ['nullable', 'integer', 'min:0'],
        ], [
            'item.required' => 'El ítem es obligatorio.',
            'item.max' => 'El ítem no puede superar los 255 caracteres.',
            'estado_item.required' => 'El estado del ítem es obligatorio.',
        ]);

        ChecklistRevisionContractual::create([
            'snapshot_revision_contractual_id' => $snapshot->id,
            'item' => $validated['item'],
            'estado_item' => $validated['estado_item'],
            'observacion' => $validated['observacion'] ?? null,
            'referencia_documental' => $validated['referencia_documental'] ?? null,
            'orden' => $validated['orden'] ?? 0,
            'user_id' => auth()->id(),
        ]);

        return redirect()
            ->route('contractual.revisiones.snapshots.checklist.index', [$revision, $snapshot])
            ->with('success', 'Ítem agregado correctamente.');
    }
}
