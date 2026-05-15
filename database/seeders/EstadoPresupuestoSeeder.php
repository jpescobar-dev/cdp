<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoPresupuestoSeeder extends Seeder
{
    public function run(): void
    {
        $tabla = 'expedientes_presupuestarios';

        $estados = [
            ['nombre' => 'Ingresado', 'descripcion' => 'Expediente creado y pendiente de revisión.', 'orden' => 1, 'es_final' => false, 'genera_tarea' => true, 'genera_notificacion' => true],
            ['nombre' => 'En revisión', 'descripcion' => 'Expediente en revisión presupuestaria.', 'orden' => 2, 'es_final' => false, 'genera_tarea' => true, 'genera_notificacion' => true],
            ['nombre' => 'Aprobado', 'descripcion' => 'Expediente aprobado para emisión.', 'orden' => 3, 'es_final' => false, 'genera_tarea' => true, 'genera_notificacion' => true],
            ['nombre' => 'Emitido', 'descripcion' => 'Documento emitido y proceso finalizado.', 'orden' => 4, 'es_final' => true, 'genera_tarea' => false, 'genera_notificacion' => true],
        ];

        foreach ($estados as $estado) {
            DB::table('estados')->updateOrInsert(
                ['nombre' => $estado['nombre'], 'tabla_referencia' => $tabla],
                array_merge($estado, ['tabla_referencia' => $tabla, 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
