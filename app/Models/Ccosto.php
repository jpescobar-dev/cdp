<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ccosto extends Model
{
    protected $table = 'ccostos';
    protected $primaryKey = 'ccosto';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['ccosto', 'nombre', 'cfinanciero'];
}
