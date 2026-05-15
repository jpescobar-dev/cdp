<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $table = 'proyectos';

    protected $fillable = [
        'proyecto', 'descripcion', 'codigo',
        'fecha_inicio', 'fecha_termino',
        'avance', 'monto_estimado', 'monto_asignado',
        'cfinanciero_id', 'estado_id',
    ];
}
