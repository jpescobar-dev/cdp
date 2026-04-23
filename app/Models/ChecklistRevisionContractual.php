<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistRevisionContractual extends Model
{
    protected $table = 'checklist_revision_contractual';

    protected $fillable = [
        'snapshot_revision_contractual_id',
        'item',
        'estado_item',
        'observacion',
        'referencia_documental',
        'orden',
        'user_id',
    ];

    public function snapshot(): BelongsTo
    {
        return $this->belongsTo(SnapshotRevisionContractual::class, 'snapshot_revision_contractual_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
