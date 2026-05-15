<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expedientes_presupuestarios', function (Blueprint $table) {
            $table->dropForeign(['solicitante_rut']);
            $table->string('solicitante_rut', 12)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('expedientes_presupuestarios', function (Blueprint $table) {
            $table->string('solicitante_rut', 12)->nullable(false)->change();
            $table->foreign('solicitante_rut')->references('rut')->on('funcionarios')->onUpdate('cascade')->onDelete('restrict');
        });
    }
};
