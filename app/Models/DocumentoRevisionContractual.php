<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoRevisionContractual extends Model
{
    protected $table = 'documentos_revision_contractual';

    protected $fillable = [
        'revision_contractual_id',
        'nombre_original',
        'nombre_archivo',
        'ruta',
        'mime_type',
        'tamano',
        'extension',
        'tipo_documento',
        'hash_archivo',
        'texto_extraido',
        'extraccion_estado',
        'tiene_texto_extraible',
        'es_vigente',
        'user_id',
    ];

    protected $casts = [
        'es_vigente' => 'boolean',
        'tiene_texto_extraible' => 'boolean',
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
