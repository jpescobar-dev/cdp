<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MesaAyudaExtraccion extends Model
{
    protected $table = 'mesa_ayuda_extracciones';

    protected $fillable = [
        'fecha_inicio',
        'fecha_termino',
        'estado',
        'ejecutado_por',
        'total_detectados',
        'total_importados',
        'total_errores',
        'ruta_json',
        'mensaje_error',
        'metadata',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_termino' => 'datetime',
        'metadata' => 'array',
    ];

    public function requerimientos()
    {
        return $this->hasMany(MesaAyudaRequerimiento::class, 'extraccion_id');
    }

    public function ejecutor()
    {
        return $this->belongsTo(Funcionario::class, 'ejecutado_por', 'rut');
    }
}
