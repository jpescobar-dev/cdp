<?php

namespace App\Services\Contractual;

use App\Models\Estado;
use App\Models\RevisionContractual;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RevisionContractualService
{
    public function crear(array $data): RevisionContractual
    {
        $estadoId = Estado::obtenerId('revisiones_contractuales', 'BORRADOR');

        return RevisionContractual::create([
            'titulo' => $data['titulo'],
            'descripcion' => $data['descripcion'] ?? null,
            'estado_id' => $estadoId,
            'user_id' => Auth::id(),
            'codigo' => $this->generarCodigo(),
        ]);
    }

    public function cambiarEstado(RevisionContractual $revision, string $nombreEstado): RevisionContractual
    {
        $estadoId = Estado::obtenerId('revisiones_contractuales', $nombreEstado);

        if (!$estadoId) {
            throw new \RuntimeException("No existe el estado {$nombreEstado} para revisiones_contractuales.");
        }

        $revision->update([
            'estado_id' => $estadoId,
        ]);

        return $revision->fresh();
    }

    protected function generarCodigo(): string
    {
        return 'REV-' . now()->format('Ymd-His') . '-' . Str::upper(Str::random(5));
    }
}