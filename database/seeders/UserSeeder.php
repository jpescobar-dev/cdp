<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin_zonal@pjud.cl'],
            [
                'name' => 'Admin_Zonal',
                'password' => Hash::make('12345678'),
                'activo' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'operador@pjud.cl'],
            [
                'name' => 'Operador Activo',
                'password' => Hash::make('12345678'),
                'activo' => true,
            ]
        );
        User::updateOrCreate(
            ['email' => 'jefe_adquisiciones@pjud.cl'],
            [
                'name' => 'Jefe Adquisiciones',
                'password' => Hash::make('12345678'),
                'activo' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'inactivo@pjud.cl'],
            [
                'name' => 'Usuario Inactivo',
                'password' => Hash::make('12345678'),
                'activo' => false,
            ]
        );
    }
}