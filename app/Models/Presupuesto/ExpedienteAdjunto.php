<?php

namespace App\Models\Presupuesto;

use App\Models\Funcionario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpedienteAdjunto extends Model
{
    protected $table = 'expediente_adjuntos';

    protected $fillable = [
        'expediente_id',
        'nombre_archivo',
        'ruta',
        'tipo',
        'subido_por',
    ];

    public function expediente(): BelongsTo
    {
        return $this->belongsTo(ExpedientePresupuestario::class, 'expediente_id');
    }

    public function usuarioCarga(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class, 'subido_por', 'rut');
    }

    public function subidoPor(): BelongsTo
    {
        return $this->usuarioCarga();
    }
}
