<?php

namespace App\Models\Presupuesto;

use App\Models\Estado;
use Illuminate\Database\Eloquent\Model;

class TransicionEstado extends Model
{
    protected $table = 'transiciones_estados';

    protected $fillable = [
        'estado_origen_id',
        'estado_destino_id',
        'tabla_referencia',
        'rol_permitido',
        'requiere_comentario',
        'genera_tarea',
        'genera_notificacion',
        'activo',
    ];

    protected $casts = [
        'requiere_comentario' => 'boolean',
        'genera_tarea' => 'boolean',
        'genera_notificacion' => 'boolean',
        'activo' => 'boolean',
    ];

    public function estadoOrigen()
    {
        return $this->belongsTo(Estado::class, 'estado_origen_id');
    }

    public function estadoDestino()
    {
        return $this->belongsTo(Estado::class, 'estado_destino_id');
    }
}
