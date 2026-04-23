<?php

namespace App\Models\Presupuesto;

use App\Models\Funcionario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpedienteAdjunto extends Model
{
    use HasFactory;

    protected $table = 'expediente_adjuntos';

    protected $fillable = [
        'expediente_id',
        'nombre_archivo',
        'ruta',
        'tipo',
        'subido_por',
    ];

    public function expediente()
    {
        return $this->belongsTo(ExpedientePresupuestario::class, 'expediente_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Funcionario::class, 'subido_por', 'rut');
    }
}
