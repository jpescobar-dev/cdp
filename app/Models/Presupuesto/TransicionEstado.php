<?php

namespace App\Models\Presupuesto;

use App\Models\Estado;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransicionEstado extends Model
{
    use HasFactory;

    protected $table = 'transiciones_estados';

    protected $fillable = [
        'estado_origen_id',
        'estado_destino_id',
        'rol_permitido',
        'requiere_comentario',
    ];

    protected $casts = [
        'requiere_comentario' => 'boolean',
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
