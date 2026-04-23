<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@pjud.cl'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('12345678'),
                'activo' => true,
            ]
        );
    }
}