<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Funcionario extends Model
{
    use SoftDeletes;

    protected $table = 'funcionarios';
    protected $primaryKey = 'rut';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'rut',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'nombre_completo',
        'email',
        'telefono',
        'cargo',
        'ccosto',
        'cfinanciero',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function expedientesSolicitante(): HasMany
    {
        return $this->hasMany(\App\Models\Presupuesto\ExpedientePresupuestario::class, 'solicitante_rut', 'rut');
    }

    public function expedientesResponsable(): HasMany
    {
        return $this->hasMany(\App\Models\Presupuesto\ExpedientePresupuestario::class, 'responsable_rut', 'rut');
    }
}
