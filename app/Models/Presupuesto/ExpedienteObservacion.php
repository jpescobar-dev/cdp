<?php

namespace App\Models\Presupuesto;

use App\Models\Funcionario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpedienteObservacion extends Model
{
    protected $table = 'expediente_observaciones';

    protected $fillable = [
        'expediente_id',
        'usuario_rut',
        'observacion',
        'resuelta',
    ];

    protected $casts = [
        'resuelta' => 'boolean',
    ];

    public function expediente(): BelongsTo
    {
        return $this->belongsTo(ExpedientePresupuestario::class, 'expediente_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class, 'usuario_rut', 'rut');
    }
}
