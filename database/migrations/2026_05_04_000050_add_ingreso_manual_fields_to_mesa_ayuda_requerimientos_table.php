<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mesa_ayuda_requerimientos', function (Blueprint $table) {
            if (!Schema::hasColumn('mesa_ayuda_requerimientos', 'es_ingreso_manual')) {
                $table->boolean('es_ingreso_manual')->default(false)->after('origen');
            }

            if (!Schema::hasColumn('mesa_ayuda_requerimientos', 'usuario_creador_rut')) {
                $table->string('usuario_creador_rut', 12)->nullable()->after('es_ingreso_manual');
            }

            if (!Schema::hasColumn('mesa_ayuda_requerimientos', 'fecha_ingreso_manual')) {
                $table->timestamp('fecha_ingreso_manual')->nullable()->after('usuario_creador_rut');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mesa_ayuda_requerimientos', function (Blueprint $table) {
            if (Schema::hasColumn('mesa_ayuda_requerimientos', 'fecha_ingreso_manual')) {
                $table->dropColumn('fecha_ingreso_manual');
            }
            if (Schema::hasColumn('mesa_ayuda_requerimientos', 'usuario_creador_rut')) {
                $table->dropColumn('usuario_creador_rut');
            }
            if (Schema::hasColumn('mesa_ayuda_requerimientos', 'es_ingreso_manual')) {
                $table->dropColumn('es_ingreso_manual');
            }
        });
    }
};
