<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CfinancierosSeeder extends Seeder
{
public function run(): void
    {
        DB::table('cfinancieros')->insert([
            [
                'cfinanciero' => '1400',
                'nombre' => 'Administracion Zonal',

            ],
            [
                'cfinanciero' => '1401',
                'nombre' => 'Garantía',

            ],
            [
            'cfinanciero' => '1402',
            'nombre' => 'Oral',
            ],
            [
            'cfinanciero' => '1431',
            'nombre' => 'Laboral',
            ],
            [
            'cfinanciero' => '1451',
            'nombre' => 'Familia',
            ],
            [
            'cfinanciero' => '1471',
            'nombre' => 'Competencia Común',
            ]
            
        ]);
    }
}