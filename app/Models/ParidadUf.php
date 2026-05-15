<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParidadUf extends Model
{
    protected $table = 'paridad_ufs';

    protected $fillable = ['fecha', 'valor'];

    protected $casts = [
        'fecha' => 'date',
        'valor' => 'decimal:4',
    ];
}
