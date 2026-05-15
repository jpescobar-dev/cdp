<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgenteInteraccion extends Model
{
    protected $table = 'agente_interacciones';

    protected $fillable = [
        'agente',
        'modelo',
        'mesa_ayuda_requerimiento_id',
        'cdp_borrador_id',
        'prompt',
        'respuesta',
        'entrada_json',
        'salida_json',
        'estado',
        'error',
        'tokens_entrada',
        'tokens_salida',
    ];

    protected $casts = [
        'entrada_json' => 'array',
        'salida_json' => 'array',
        'tokens_entrada' => 'integer',
        'tokens_salida' => 'integer',
    ];

    public function requerimientoMesaAyuda()
    {
        return $this->belongsTo(MesaAyudaRequerimiento::class, 'mesa_ayuda_requerimiento_id');
    }

    public function cdpBorrador()
    {
        return $this->belongsTo(CdpBorrador::class, 'cdp_borrador_id');
    }
}
