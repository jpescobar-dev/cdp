<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expediente_tareas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expedientes_presupuestarios')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('asignado_a', 12);
            $table->string('creado_por', 12);
            $table->timestamp('fecha_vencimiento')->nullable();
            $table->string('estado', 30)->default('pendiente');
            $table->timestamp('fecha_cierre')->nullable();
            $table->timestamps();
            $table->foreign('asignado_a')->references('rut')->on('funcionarios')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('creado_por')->references('rut')->on('funcionarios')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expediente_tareas');
    }
};
