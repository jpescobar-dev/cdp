<?php

namespace App\Models\Presupuesto;

use App\Models\Estado;
use App\Models\Funcionario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpedienteHistorial extends Model
{
    protected $table = 'expediente_historial';

    protected $fillable = [
        'expediente_id',
        'estado_id',
        'usuario_rut',
        'comentario',
        'fecha_cambio',
    ];

    protected $casts = [
        'fecha_cambio' => 'datetime',
    ];

    public function expediente(): BelongsTo
    {
        return $this->belongsTo(ExpedientePresupuestario::class, 'expediente_id');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class, 'usuario_rut', 'rut');
    }
}
