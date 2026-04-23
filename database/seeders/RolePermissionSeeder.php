<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'ver dashboard',
            'gestionar usuarios',
            'ver usuarios',
            'crear usuarios',
            'editar usuarios',
            'eliminar usuarios',
            'asignar roles',  
            'ver revisiones contractuales',       
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $administradorZonal = Role::firstOrCreate(['name' => 'Administrador Zonal']);
        $operador = Role::firstOrCreate(['name' => 'Operador']);
        $consulta = Role::firstOrCreate(['name' => 'Consulta']);

        $superAdmin->syncPermissions(Permission::all());

        $administradorZonal->syncPermissions([
            'ver dashboard',
            'gestionar usuarios',
            'ver usuarios',
            'crear usuarios',
            'editar usuarios',
            'asignar roles',
            'ver revisiones contractuales', 
        ]);

        $operador->syncPermissions([
            'ver dashboard',
        ]);

        $consulta->syncPermissions([
            'ver dashboard',
        ]);

        $user = User::where('email', 'admin@pjud.cl')->first();

        if ($user) {
            $user->syncRoles(['Super Admin']);
        }
    }
}