<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadosExpedientesSeeder extends Seeder
{
    public function run(): void
    {
        $registros = [
            [
                'nombre' => 'Ingresado',
                'descripcion' => 'Expediente creado y pendiente de revisión.',
                'tabla_referencia' => 'expedientes_presupuestarios',
                'orden' => 1,
                'es_final' => false,
                'genera_tarea' => true,
                'genera_notificacion' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'En revisión',
                'descripcion' => 'Expediente en proceso de revisión.',
                'tabla_referencia' => 'expedientes_presupuestarios',
                'orden' => 2,
                'es_final' => false,
                'genera_tarea' => true,
                'genera_notificacion' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Aprobado',
                'descripcion' => 'Expediente aprobado para emisión.',
                'tabla_referencia' => 'expedientes_presupuestarios',
                'orden' => 3,
                'es_final' => false,
                'genera_tarea' => true,
                'genera_notificacion' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Emitido',
                'descripcion' => 'Documento emitido y flujo finalizado.',
                'tabla_referencia' => 'expedientes_presupuestarios',
                'orden' => 4,
                'es_final' => true,
                'genera_tarea' => false,
                'genera_notificacion' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($registros as $registro) {
            DB::table('estados')->updateOrInsert(
                [
                    'nombre' => $registro['nombre'],
                    'tabla_referencia' => $registro['tabla_referencia'],
                ],
                $registro
            );
        }
    }
}
