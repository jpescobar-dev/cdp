<?php

namespace App\Models\Presupuesto;

use App\Models\Funcionario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpedienteTarea extends Model
{
    use HasFactory;

    protected $table = 'expediente_tareas';

    protected $fillable = [
        'expediente_id',
        'titulo',
        'descripcion',
        'asignado_a',
        'creado_por',
        'fecha_vencimiento',
        'estado',
        'fecha_cierre',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    public function expediente()
    {
        return $this->belongsTo(ExpedientePresupuestario::class, 'expediente_id');
    }

    public function asignado()
    {
        return $this->belongsTo(Funcionario::class, 'asignado_a', 'rut');
    }

    public function creador()
    {
        return $this->belongsTo(Funcionario::class, 'creado_por', 'rut');
    }
}
