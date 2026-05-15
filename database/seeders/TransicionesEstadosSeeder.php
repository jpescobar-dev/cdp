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

        $transiciones = [
            ['origen' => 'Ingresado', 'destino' => 'En revisión', 'rol' => 'revisor_emisor', 'genera_tarea' => true, 'genera_notificacion' => true],
            ['origen' => 'En revisión', 'destino' => 'Aprobado', 'rol' => 'revisor_emisor', 'genera_tarea' => true, 'genera_notificacion' => true],
            ['origen' => 'Aprobado', 'destino' => 'Emitido', 'rol' => 'revisor_emisor', 'genera_tarea' => false, 'genera_notificacion' => true],
        ];

        foreach ($transiciones as $transicion) {
            DB::table('transiciones_estados')->updateOrInsert(
                [
                    'estado_origen_id' => $estados[$transicion['origen']],
                    'estado_destino_id' => $estados[$transicion['destino']],
                    'tabla_referencia' => $tabla,
                    'rol_permitido' => $transicion['rol'],
                ],
                [
                    'requiere_comentario' => false,
                    'genera_tarea' => $transicion['genera_tarea'],
                    'genera_notificacion' => $transicion['genera_notificacion'],
                    'activo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
