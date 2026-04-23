<?php

namespace App\Services\Contractual;

use App\Models\RevisionContractual;
use App\Models\SnapshotRevisionContractual;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SnapshotRevisionContractualService
{
    public function crear(RevisionContractual $revision, array $resultado = [], ?string $resumen = null): SnapshotRevisionContractual
    {
        return DB::transaction(function () use ($revision, $resultado, $resumen) {
            $ultimoNumero = (int) $revision->snapshots()->max('numero_version');
            $nuevoNumero = $ultimoNumero + 1;

            $revision->snapshots()->update(['es_actual' => false]);

            return SnapshotRevisionContractual::create([
                'revision_contractual_id' => $revision->id,
                'numero_version' => $nuevoNumero,
                'tipo_ejecucion' => 'manual',
                'resumen' => $resumen,
                'json_resultado' => $resultado,
                'es_actual' => true,
                'user_id' => Auth::id(),
            ]);
        });
    }
}