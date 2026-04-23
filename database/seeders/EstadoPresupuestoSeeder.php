<?php

namespace Database\Seeders;

use App\Models\Estado;
use Illuminate\Database\Seeder;

class EstadoPresupuestoSeeder extends Seeder
{
    public function run(): void
    {
        $tabla = 'presupuesto';

        $estados = [
            [
                'nombre' => 'DIGITADO',
                'descripcion' => 'DIGITADO',
                'tabla_referencia' => $tabla,
            ],
            [
                'nombre' => 'IMPORTADO',
                'descripcion' => 'IMPORTACION MASIVA',
                'tabla_referencia' => $tabla,
            ],
            [
                'nombre' => 'PREAFECTACION',
                'descripcion' => 'FICHA INICIO',
                'tabla_referencia' => $tabla,
            ],
            [
                'nombre' => 'REVISION',
                'descripcion' => 'EN REVISION',
                'tabla_referencia' => $tabla,
            ],
            [
                'nombre' => 'APROBADO',
                'descripcion' => 'APROBADO',
                'tabla_referencia' => $tabla,
            ],
            [
                'nombre' => 'RECHAZADO',
                'descripcion' => 'RECHAZADO',
                'tabla_referencia' => $tabla,
            ],
            [
                'nombre' => 'AFECTACION',
                'descripcion' => 'ORDEN DE COMPRA',
                'tabla_referencia' => $tabla,
            ],
            [
                'nombre' => 'OBLIGACION',
                'descripcion' => 'CERTIFICADO DE DISPONIBILIDAD PRESUPUESTARIA',
                'tabla_referencia' => $tabla,
            ],
            [
                'nombre' => 'PAGADA',
                'descripcion' => 'EGRESO',
                'tabla_referencia' => $tabla,
            ],
            [
                'nombre' => 'TERMINADA',
                'descripcion' => 'EBOOK',
                'tabla_referencia' => $tabla,
            ],
        ];

        foreach ($estados as $estado) {
            Estado::updateOrCreate(
                [
                    'nombre' => $estado['nombre'],
                    'tabla_referencia' => $estado['tabla_referencia'],
                ],
                [
                    'descripcion' => $estado['descripcion'],
                ]
            );
        }
    }
}