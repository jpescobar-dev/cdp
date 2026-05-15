<?php

namespace App\Models\Presupuesto;

use App\Models\Ccosto;
use App\Models\CdpBorrador;
use App\Models\Cfinanciero;
use App\Models\Estado;
use App\Models\Funcionario;
use App\Models\MesaAyudaRequerimiento;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpedientePresupuestario extends Model
{
    use SoftDeletes;

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
        'anio' => 'integer',
        'fecha_ingreso' => 'datetime',
        'fecha_aprobacion' => 'datetime',
        'fecha_emision' => 'datetime',
        'monto' => 'decimal:2',
        'total_moneda_compra' => 'decimal:2',
    ];

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class, 'solicitante_rut', 'rut');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class, 'responsable_rut', 'rut');
    }

    public function centroCosto(): BelongsTo
    {
        return $this->belongsTo(Ccosto::class, 'ccosto', 'ccosto');
    }

    public function centroFinanciero(): BelongsTo
    {
        return $this->belongsTo(Cfinanciero::class, 'cfinanciero', 'cfinanciero');
    }

    public function historial(): HasMany
    {
        return $this->hasMany(ExpedienteHistorial::class, 'expediente_id');
    }

    public function tareas(): HasMany
    {
        return $this->hasMany(ExpedienteTarea::class, 'expediente_id');
    }

    public function observaciones(): HasMany
    {
        return $this->hasMany(ExpedienteObservacion::class, 'expediente_id');
    }

    public function adjuntos(): HasMany
    {
        return $this->hasMany(ExpedienteAdjunto::class, 'expediente_id');
    }

    public function requerimientoMesaAyuda(): HasOne
    {
        return $this->hasOne(MesaAyudaRequerimiento::class, 'expediente_presupuestario_id');
    }

    public function cdpBorradores(): HasMany
    {
        return $this->hasMany(CdpBorrador::class, 'expediente_presupuestario_id');
    }
}
