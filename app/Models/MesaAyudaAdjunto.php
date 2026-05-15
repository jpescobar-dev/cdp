<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MesaAyudaAdjunto extends Model
{
    protected $table = 'mesa_ayuda_adjuntos';

    protected $fillable = [
        'mesa_ayuda_requerimiento_id',
        'nombre_archivo',
        'ruta_local',
        'url_origen',
        'tipo_mime',
        'tamano_bytes',
        'hash_archivo',
        'descargado',
        'texto_extraido',
        'clasificacion_documento',
        'metadata',
    ];

    protected $casts = [
        'descargado' => 'boolean',
        'metadata' => 'array',
    ];

    public function requerimiento()
    {
        return $this->belongsTo(MesaAyudaRequerimiento::class, 'mesa_ayuda_requerimiento_id');
    }
}
