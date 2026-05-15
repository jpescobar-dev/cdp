<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cdp_solicitudes', function (Blueprint $table) {
            $table->string('ccosto', 10)->nullable()->after('unidad_requirente');
            $table->string('requerimiento')->nullable()->after('ccosto');
            $table->enum('tipo_gasto1', ['GO', 'INI'])->nullable()->after('proveedor');
            $table->enum('tipo_gasto2', ['TRANSITORIO', 'PERMANENTE'])->nullable()->after('tipo_gasto1');
            $table->unsignedBigInteger('proyecto_id')->nullable()->after('tipo_gasto2');
        });
    }

    public function down(): void
    {
        Schema::table('cdp_solicitudes', function (Blueprint $table) {
            $table->dropColumn(['ccosto', 'requerimiento', 'tipo_gasto1', 'tipo_gasto2', 'proyecto_id']);
        });
    }
};
