<?php

namespace App\Models\Presupuesto;

use App\Models\Funcionario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpedienteTarea extends Model
{
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

    public function expediente(): BelongsTo
    {
        return $this->belongsTo(ExpedientePresupuestario::class, 'expediente_id');
    }

    public function asignado(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class, 'asignado_a', 'rut');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class, 'creado_por', 'rut');
    }
}
