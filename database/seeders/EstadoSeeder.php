<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EstadoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            EstadoPresupuestoSeeder::class,
            EstadoRevisionContractualSeeder::class,
            EstadoHallazgoRevisionSeeder::class,
        ]);
    }
}