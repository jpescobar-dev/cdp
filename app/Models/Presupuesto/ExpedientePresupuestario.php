<?php

namespace App\Models\Presupuesto;

use App\Models\Estado;
use App\Models\Funcionario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpedientePresupuestario extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'expedientes_presupuestarios';

    protected $fillable = [
        'correlativo',
        'anio',
        'solicitante_rut',
        'responsable_rut',
        'ccosto',
        'cfinanciero',
        'cuenta_presupuestaria',
        'denominacion',
        'monto',
        'moneda',
        'total_moneda_compra',
        'glosa',
        'caracter_gasto',
        'medio_solicitud',
        'numero_requerimiento',
        'estado_id',
        'fecha_ingreso',
        'fecha_aprobacion',
        'fecha_emision',
        'numero_cdp',
        'archivo_pdf',
    ];

    protected $casts = [
        'fecha_ingreso' => 'datetime',
        'fecha_aprobacion' => 'datetime',
        'fecha_emision' => 'datetime',
        'monto' => 'decimal:2',
        'total_moneda_compra' => 'decimal:2',
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function solicitante()
    {
        return $this->belongsTo(Funcionario::class, 'solicitante_rut', 'rut');
    }

    public function responsable()
    {
        return $this->belongsTo(Funcionario::class, 'responsable_rut', 'rut');
    }

    public function historial()
    {
        return $this->hasMany(ExpedienteHistorial::class, 'expediente_id');
    }

    public function tareas()
    {
        return $this->hasMany(ExpedienteTarea::class, 'expediente_id');
    }

    public function observaciones()
    {
        return $this->hasMany(ExpedienteObservacion::class, 'expediente_id');
    }

    public function adjuntos()
    {
        return $this->hasMany(ExpedienteAdjunto::class, 'expediente_id');
    }
}
