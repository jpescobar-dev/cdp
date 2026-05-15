<?php

namespace Database\Seeders;

use App\Models\Estado;
use Illuminate\Database\Seeder;

class MesaAyudaEstadosSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            ['nombre' => 'CAPTURADO', 'descripcion' => 'Requerimiento capturado desde Mesa de Ayuda.', 'orden' => 10],
            ['nombre' => 'CLASIFICADO', 'descripcion' => 'Requerimiento clasificado por el sistema.', 'orden' => 20],
            ['nombre' => 'NO_CDP', 'descripcion' => 'No corresponde a certificado de disponibilidad presupuestaria.', 'orden' => 30],
            ['nombre' => 'POSIBLE_CDP', 'descripcion' => 'Posible CDP; requiere revisión del usuario.', 'orden' => 40, 'genera_tarea' => true],
            ['nombre' => 'CDP_REQUIERE_DATOS', 'descripcion' => 'Faltan datos para generar borrador CDP.', 'orden' => 50, 'genera_tarea' => true, 'genera_notificacion' => true],
            ['nombre' => 'CDP_BORRADOR_GENERADO', 'descripcion' => 'Borrador CDP generado por el agente.', 'orden' => 60, 'genera_notificacion' => true],
            ['nombre' => 'EN_REVISION_USUARIO', 'descripcion' => 'Borrador en revisión del usuario.', 'orden' => 70, 'genera_tarea' => true],
            ['nombre' => 'APROBADO_USUARIO', 'descripcion' => 'Aprobado por el usuario responsable.', 'orden' => 80],
            ['nombre' => 'OBSERVADO_USUARIO', 'descripcion' => 'Observado por el usuario; requiere corrección.', 'orden' => 90, 'genera_tarea' => true],
            ['nombre' => 'RESPONDIDO', 'descripcion' => 'Requerimiento respondido en Mesa de Ayuda.', 'orden' => 100, 'es_final' => true],
            ['nombre' => 'ERROR', 'descripcion' => 'Error en captura, clasificación o procesamiento.', 'orden' => 999, 'es_final' => true],
        ];

        foreach ($estados as $estado) {
            Estado::updateOrCreate(
                [
                    'nombre' => $estado['nombre'],
                    'tabla_referencia' => 'mesa_ayuda_requerimientos',
                ],
                [
                    'descripcion' => $estado['descripcion'],
                    'orden' => $estado['orden'],
                    'es_final' => $estado['es_final'] ?? false,
                    'genera_tarea' => $estado['genera_tarea'] ?? false,
                    'genera_notificacion' => $estado['genera_notificacion'] ?? false,
                ]
            );
        }
    }
}
