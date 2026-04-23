<?php

namespace App\Http\Controllers\Contractual;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contractual\StoreRevisionContractualRequest;
use App\Http\Requests\Contractual\UpdateRevisionContractualRequest;
use App\Models\Estado;
use App\Models\RevisionContractual;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RevisionContractualController extends Controller
{
    public function index(): View
    {
        $revisiones = RevisionContractual::with(['estado', 'usuario'])
            ->latest()
            ->get();

        return view('contractual.revisiones.index', compact('revisiones'));
    }

    public function create(): View
    {
        return view('contractual.revisiones.create');
    }

    public function store(StoreRevisionContractualRequest $request): RedirectResponse
    {
        $estadoInicialId = Estado::query()
            ->where('tabla_referencia', 'revisiones_contractuales')
            ->where('nombre', 'BORRADOR')
            ->value('id');

        if (!$estadoInicialId) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'No existe el estado inicial BORRADOR para revisiones contractuales.');
        }

        $revision = RevisionContractual::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'estado_id' => $estadoInicialId,
            'user_id' => auth()->id(),
        ]);

        return redirect()
            ->route('contractual.revisiones.show', $revision)
            ->with('success', 'La revisión contractual fue creada correctamente.');
    }

    public function show(RevisionContractual $revision): View
    {
        $revision->load([
            'estado',
            'usuario',
            'documentos.usuario',
            'snapshots.usuario',
        ]);

        return view('contractual.revisiones.show', compact('revision'));
    }

    public function edit(RevisionContractual $revision): View
    {
        $estados = Estado::query()
            ->where('tabla_referencia', 'revisiones_contractuales')
            ->orderBy('nombre')
            ->get();

        return view('contractual.revisiones.edit', compact('revision', 'estados'));
    }

    public function update(UpdateRevisionContractualRequest $request, RevisionContractual $revision): RedirectResponse
    {
        $revision->update([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'estado_id' => $request->estado_id,
        ]);

        return redirect()
            ->route('contractual.revisiones.index')
            ->with('success', 'La revisión contractual fue actualizada correctamente.');
    }

    public function destroy(RevisionContractual $revision): RedirectResponse
    {
        $revision->delete();

        return redirect()
            ->route('contractual.revisiones.index')
            ->with('success', 'La revisión contractual fue eliminada correctamente.');
    }
}