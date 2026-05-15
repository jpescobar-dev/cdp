<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MesaAyudaRespuesta extends Model
{
    protected $table = 'mesa_ayuda_respuestas';

    protected $fillable = [
        'mesa_ayuda_requerimiento_id',
        'cdp_borrador_id',
        'texto_respuesta',
        'estado',
        'aprobado_por',
        'fecha_aprobacion',
        'fecha_envio',
        'folio_externo',
        'snapshot_envio',
        'error_envio',
    ];

    protected $casts = [
        'fecha_aprobacion' => 'datetime',
        'fecha_envio' => 'datetime',
        'snapshot_envio' => 'array',
    ];

    public function requerimientoMesaAyuda()
    {
        return $this->belongsTo(MesaAyudaRequerimiento::class, 'mesa_ayuda_requerimiento_id');
    }

    public function cdpBorrador()
    {
        return $this->belongsTo(CdpBorrador::class, 'cdp_borrador_id');
    }

    public function aprobador()
    {
        return $this->belongsTo(Funcionario::class, 'aprobado_por', 'rut');
    }
}
