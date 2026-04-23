<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('expediente_tareas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expedientes_presupuestarios')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('titulo', 200);
            $table->text('descripcion')->nullable();
            $table->string('asignado_a', 12);
            $table->string('creado_por', 12);
            $table->timestamp('fecha_vencimiento')->nullable()->index();
            $table->string('estado', 30)->default('pendiente')->index();
            $table->timestamp('fecha_cierre')->nullable();
            $table->timestamps();

            $table->foreign('asignado_a')->references('rut')->on('funcionarios')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreign('creado_por')->references('rut')->on('funcionarios')->restrictOnDelete()->cascadeOnUpdate();
            $table->index(['expediente_id', 'estado'], 'idx_tareas_expediente_estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expediente_tareas');
    }
};
