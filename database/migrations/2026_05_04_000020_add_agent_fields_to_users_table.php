<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'tipo_usuario')) {
                $table->string('tipo_usuario', 30)->default('humano')->after('id');
            }

            if (! Schema::hasColumn('users', 'es_agente')) {
                $table->boolean('es_agente')->default(false)->after('tipo_usuario');
            }

            if (! Schema::hasColumn('users', 'codigo_agente')) {
                $table->string('codigo_agente', 100)->nullable()->unique()->after('es_agente');
            }

            if (! Schema::hasColumn('users', 'puede_login')) {
                $table->boolean('puede_login')->default(true)->after('codigo_agente');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'codigo_agente')) {
                $table->dropUnique(['codigo_agente']);
            }

            foreach (['puede_login', 'codigo_agente', 'es_agente', 'tipo_usuario'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
