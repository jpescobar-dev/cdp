<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesa_ayuda_extracciones', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_termino')->nullable();
            $table->string('estado', 50)->default('pendiente')->index();
            $table->string('ejecutado_por', 12)->nullable();
            $table->integer('total_detectados')->default(0);
            $table->integer('total_importados')->default(0);
            $table->integer('total_errores')->default(0);
            $table->string('ruta_json')->nullable();
            $table->text('mensaje_error')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('ejecutado_por')
                ->references('rut')
                ->on('funcionarios')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesa_ayuda_extracciones');
    }
};
