<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expediente_adjuntos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expedientes_presupuestarios')->onDelete('cascade');
            $table->string('nombre_archivo');
            $table->string('ruta');
            $table->string('tipo', 50)->nullable();
            $table->string('subido_por', 12);
            $table->timestamps();
            $table->foreign('subido_por')->references('rut')->on('funcionarios')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expediente_adjuntos');
    }
};
