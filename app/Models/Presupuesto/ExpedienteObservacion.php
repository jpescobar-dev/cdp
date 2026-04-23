<?php

namespace App\Models\Presupuesto;

use App\Models\Funcionario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpedienteObservacion extends Model
{
    use HasFactory;

    protected $table = 'expediente_observaciones';

    protected $fillable = [
        'expediente_id',
        'usuario_rut',
        'observacion',
        'resuelta',
    ];

    protected $casts = [
        'resuelta' => 'boolean',
    ];

    public function expediente()
    {
        return $this->belongsTo(ExpedientePresupuestario::class, 'expediente_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Funcionario::class, 'usuario_rut', 'rut');
    }
}
