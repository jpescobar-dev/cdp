<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CdpSolicitud extends Model
{
    protected $table = 'cdp_solicitudes';

    protected $fillable = [
        'user_id',
        'nombre_requirente',
        'rut_requirente',
        'unidad_requirente',
        'ccosto',
        'requerimiento',
        'glosa',
        'proveedor',
        'monto_estimado',
        'moneda',
        'tipo_gasto1',
        'tipo_gasto2',
        'proyecto_id',
        'documentos',
        'estado',
        'pdf_path',
    ];

    protected $casts = [
        'monto_estimado' => 'decimal:2',
        'documentos'     => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ccosto(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Ccosto::class, 'ccosto', 'ccosto');
    }

    public function proyecto(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function nombreCdp(): string
    {
        $numero = str_pad($this->id, 3, '0', STR_PAD_LEFT);
        $anio   = $this->created_at
            ? $this->created_at->format('Y')
            : now()->format('Y');

        return "CDP-{$numero}-{$anio}";
    }

    public function montoFormateado(): string
    {
        $monto = number_format($this->monto_estimado, 0, ',', '.');
        return $this->moneda === 'UF'
            ? "UF {$monto}"
            : "$ {$monto}";
    }
}
