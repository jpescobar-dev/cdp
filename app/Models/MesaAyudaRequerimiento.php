<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MesaAyudaRequerimiento extends Model
{
    use SoftDeletes;

    protected $table = 'mesa_ayuda_requerimientos';

    protected $fillable = [
        'extraccion_id',
        'folio',
        'fecha_hora',
        'estado_externo',
        'componente',
        'tipo_requerimiento',
        'tribunal',
        'solicitado_por',
        'solicitado_para',
        'tiempo_estimado_solucion',
        'observacion_principal',
        'tipificacion',
        'url_detalle',
        'clasificacion',
        'requiere_cdp',
        'confianza_clasificacion',
        'score_clasificacion',
        'evidencias_clasificacion',
        'destino_flujo',
        'procesar_automaticamente',
        'motivo_routing',
        'estado_id',
        'expediente_presupuestario_id',
        'head_json',
        'body_json',
        'json_completo',
        'fecha_captura',
        'origen',
        'es_ingreso_manual',
        'usuario_creador_rut',
        'fecha_ingreso_manual',
        'error_captura',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'fecha_captura' => 'datetime',
        'fecha_ingreso_manual' => 'datetime',
        'requiere_cdp' => 'boolean',
        'procesar_automaticamente' => 'boolean',
        'es_ingreso_manual' => 'boolean',
        'evidencias_clasificacion' => 'array',
        'head_json' => 'array',
        'body_json' => 'array',
        'json_completo' => 'array',
    ];

    public function extraccion(): BelongsTo
    {
        return $this->belongsTo(MesaAyudaExtraccion::class, 'extraccion_id');
    }

    public function historial(): HasMany
    {
        return $this->hasMany(MesaAyudaHistorial::class, 'mesa_ayuda_requerimiento_id');
    }

    public function adjuntos(): HasMany
    {
        return $this->hasMany(MesaAyudaAdjunto::class, 'mesa_ayuda_requerimiento_id');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class);
    }

    public function expedientePresupuestario(): BelongsTo
    {
        return $this->belongsTo(ExpedientePresupuestario::class, 'expediente_presupuestario_id');
    }

    public function cdpBorradores(): HasMany
    {
        return $this->hasMany(CdpBorrador::class, 'mesa_ayuda_requerimiento_id');
    }

    public function respuestas(): HasMany
    {
        return $this->hasMany(MesaAyudaRespuesta::class, 'mesa_ayuda_requerimiento_id');
    }

    public function usuarioCreador(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class, 'usuario_creador_rut', 'rut');
    }

    public function esCdp(): bool
    {
        return $this->requiere_cdp === true
            && $this->clasificacion === 'certificado_disponibilidad_presupuestaria';
    }
}
