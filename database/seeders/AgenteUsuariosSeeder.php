<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AgenteUsuariosSeeder extends Seeder
{
    public function run(): void
    {
        $agentes = [
            'agente.orquestador' => 'Agente Orquestador',
            'agente.extractor.mesa_ayuda' => 'Agente Extractor Mesa de Ayuda',
            'agente.importador_json' => 'Agente Importador JSON',
            'agente.clasificador_cdp' => 'Agente Clasificador CDP',
            'agente.lector_documentos' => 'Agente Lector de Documentos',
            'agente.redactor_cdp' => 'Agente Redactor CDP',
            'agente.validador_cdp' => 'Agente Validador CDP',
            'agente.respuesta_mesa_ayuda' => 'Agente Respuesta Mesa de Ayuda',
        ];

        foreach ($agentes as $codigo => $nombre) {
            $user = User::query()->firstOrNew([
                'email' => $codigo . '@local.agent',
            ]);

            $user->name = $nombre;
            $user->password = $user->password ?: Hash::make(Str::random(40));
            $user->tipo_usuario = 'agente';
            $user->es_agente = true;
            $user->codigo_agente = $codigo;
            $user->puede_login = false;
            $user->save();

            if (method_exists($user, 'assignRole') && class_exists(\Spatie\Permission\Models\Role::class)) {
                $roleName = 'Agente Sistema';
                \Spatie\Permission\Models\Role::findOrCreate($roleName);
                $user->assignRole($roleName);
            }
        }
    }
}
