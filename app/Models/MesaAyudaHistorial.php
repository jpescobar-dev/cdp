<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MesaAyudaHistorial extends Model
{
    protected $table = 'mesa_ayuda_historial';

    protected $fillable = [
        'mesa_ayuda_requerimiento_id',
        'fecha',
        'hora',
        'estado_externo',
        'accion',
        'usuario_externo',
        'observacion',
        'raw_json',
    ];

    protected $casts = [
        'fecha' => 'date',
        'raw_json' => 'array',
    ];

    public function requerimiento()
    {
        return $this->belongsTo(MesaAyudaRequerimiento::class, 'mesa_ayuda_requerimiento_id');
    }
}
