<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgenteEjecucion extends Model
{
    protected $table = 'agente_ejecuciones';

    protected $fillable = [
        'uuid',
        'agente_codigo',
        'agente_nombre',
        'tipo_tarea',
        'estado',
        'solicitado_por_user_id',
        'agente_user_id',
        'mesa_ayuda_requerimiento_id',
        'expediente_presupuestario_id',
        'cdp_borrador_id',
        'input_json',
        'output_json',
        'resumen',
        'error_mensaje',
        'error_tipo',
        'stack_trace',
        'fecha_inicio',
        'fecha_termino',
        'duracion_ms',
        'intentos',
        'proximo_reintento',
        'metadata',
    ];

    protected $casts = [
        'input_json' => 'array',
        'output_json' => 'array',
        'fecha_inicio' => 'datetime',
        'fecha_termino' => 'datetime',
        'proximo_reintento' => 'datetime',
        'metadata' => 'array',
    ];

    public function solicitadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'solicitado_por_user_id');
    }

    public function agenteUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agente_user_id');
    }

    public function requerimientoMesaAyuda(): BelongsTo
    {
        return $this->belongsTo(MesaAyudaRequerimiento::class, 'mesa_ayuda_requerimiento_id');
    }

    public function expedientePresupuestario(): BelongsTo
    {
        return $this->belongsTo(ExpedientePresupuestario::class, 'expediente_presupuestario_id');
    }

    public function cdpBorrador(): BelongsTo
    {
        return $this->belongsTo(CdpBorrador::class, 'cdp_borrador_id');
    }

    public function estaFallida(): bool
    {
        return $this->estado === 'error';
    }

    public function estaCompletada(): bool
    {
        return $this->estado === 'completado';
    }
}
