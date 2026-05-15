<?php

namespace App\Services\MesaAyuda;

use App\Models\Estado;
use App\Models\ExpedientePresupuestario;
use App\Models\MesaAyudaRequerimiento;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CrearExpedientePresupuestarioDesdeRequerimientoService
{
    public function crear(MesaAyudaRequerimiento $requerimiento, ?User $usuario = null): ExpedientePresupuestario
    {
        if ($requerimiento->expediente_presupuestario_id) {
            return $requerimiento->expedientePresupuestario;
        }

        if (!$requerimiento->requiere_cdp) {
            throw new RuntimeException('El requerimiento no está marcado como CDP. Reclasifique o revise antes de crear expediente.');
        }

        return DB::transaction(function () use ($requerimiento, $usuario) {
            $estado = Estado::query()
                ->where('tabla_referencia', 'expedientes_presupuestarios')
                ->whereIn('nombre', ['DIGITADO', 'INGRESADO', 'CAPTURADO'])
                ->orderBy('orden')
                ->first();

            $correlativo = $this->generarCorrelativo();

            $expediente = ExpedientePresupuestario::create([
                'correlativo' => $correlativo,
                'anio' => now()->year,
                'solicitante_rut' => null,
                'responsable_rut' => null,
                'ccosto' => null,
                'cfinanciero' => null,
                'cuenta_presupuestaria' => 'PENDIENTE',
                'denominacion' => null,
                'monto' => 0,
                'moneda' => 'CLP',
                'total_moneda_compra' => null,
                'glosa' => $requerimiento->observacion_principal ?: ($requerimiento->tipificacion ?? 'Solicitud CDP desde Mesa de Ayuda'),
                'caracter_gasto' => null,
                'medio_solicitud' => 'Requerimiento Mesa de Ayuda',
                'numero_requerimiento' => $requerimiento->folio,
                'estado_id' => $estado?->id,
                'fecha_ingreso' => now(),
            ]);

            $requerimiento->update([
                'expediente_presupuestario_id' => $expediente->id,
                'destino_flujo' => 'expediente_presupuestario',
                'motivo_routing' => 'Expediente presupuestario creado desde requerimiento Mesa de Ayuda.',
            ]);

            return $expediente;
        });
    }

    private function generarCorrelativo(): string
    {
        $anio = now()->year;
        $ultimo = ExpedientePresupuestario::query()
            ->where('anio', $anio)
            ->lockForUpdate()
            ->count() + 1;

        return sprintf('EXP-%s-%04d', $anio, $ultimo);
    }
}
