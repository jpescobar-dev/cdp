<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SnapshotRevisionContractual extends Model
{
    protected $table = 'snapshots_revision_contractual';

    protected $fillable = [
        'revision_contractual_id',
        'numero_version',
        'tipo_ejecucion',
        'resumen',
        'json_resultado',
        'es_actual',
        'user_id',
    ];

    protected $casts = [
        'json_resultado' => 'array',
        'es_actual' => 'boolean',
    ];

    public function revision(): BelongsTo
    {
        return $this->belongsTo(RevisionContractual::class, 'revision_contractual_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}