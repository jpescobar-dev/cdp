<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $table = 'estados';

    protected $fillable = [
        'nombre',
        'descripcion',
        'tabla_referencia',
    ];

    public function scopeDeTabla(Builder $query, string $tablaReferencia): Builder
    {
        return $query->where('tabla_referencia', $tablaReferencia);
    }

    public function scopePorNombre(Builder $query, string $nombre): Builder
    {
        return $query->where('nombre', strtoupper(trim($nombre)));
    }

    public static function obtenerId(string $tablaReferencia, string $nombre): ?int
    {
        return static::query()
            ->deTabla($tablaReferencia)
            ->porNombre($nombre)
            ->value('id');
    }
}