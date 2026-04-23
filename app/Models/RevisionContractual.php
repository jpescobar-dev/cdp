<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RevisionContractual extends Model
{
    protected $table = 'revisiones_contractuales';

    protected $fillable = [
        'titulo',
        'descripcion',
        'estado_id',
        'user_id',
    ];

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(DocumentoRevisionContractual::class, 'revision_contractual_id')
            ->latest();
    }

    public function snapshots(): HasMany
    {
        return $this->hasMany(SnapshotRevisionContractual::class, 'revision_contractual_id')
            ->latest();
    }
}