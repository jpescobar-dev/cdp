<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HallazgoRevisionContractual extends Model
{
    protected $table = 'hallazgos_revision_contractual';

    protected $fillable = [
        'snapshot_revision_contractual_id',
        'estado_id',
        'titulo',
        'tipo_hallazgo',
        'tipo_riesgo',
        'nivel_criticidad',
        'hecho_acreditado',
        'observacion',
        'fundamento_documental',
        'consecuencia_posible',
        'recomendacion',
        'user_id',
    ];

    public function snapshot(): BelongsTo
    {
        return $this->belongsTo(SnapshotRevisionContractual::class, 'snapshot_revision_contractual_id');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
