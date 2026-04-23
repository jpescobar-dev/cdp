<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransicionesEstadosSeeder extends Seeder
{
    public function run(): void
    {
        $tabla = 'expedientes_presupuestarios';

        $estados = DB::table('estados')
            ->where('tabla_referencia', $tabla)
            ->pluck('id', 'nombre');

        DB::table('transiciones_estados')->updateOrInsert(
            [
                'estado_origen_id' => $estados['Ingresado'],
                'estado_destino_id' => $estados['En revisión'],
                'tabla_referencia' => $tabla,
                'rol_permitido' => 'revisor_emisor',
            ],
            [
                'requiere_comentario' => false,
                'genera_tarea' => true,
                'genera_notificacion' => true,
                'activo' => true,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        DB::table('transiciones_estados')->updateOrInsert(
            [
                'estado_origen_id' => $estados['En revisión'],
                'estado_destino_id' => $estados['Aprobado'],
                'tabla_referencia' => $tabla,
                'rol_permitido' => 'revisor_emisor',
            ],
            [
                'requiere_comentario' => false,
                'genera_tarea' => true,
                'genera_notificacion' => true,
                'activo' => true,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        DB::table('transiciones_estados')->updateOrInsert(
            [
                'estado_origen_id' => $estados['Aprobado'],
                'estado_destino_id' => $estados['Emitido'],
                'tabla_referencia' => $tabla,
                'rol_permitido' => 'revisor_emisor',
            ],
            [
                'requiere_comentario' => false,
                'genera_tarea' => false,
                'genera_notificacion' => true,
                'activo' => true,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}