<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catalogo extends Model
{
    protected $table = 'catalogos';
    protected $primaryKey = 'catalogo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['catalogo', 'nombre', 'descripcion', 'estado', 'item'];
}
