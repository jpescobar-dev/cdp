<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estados', function (Blueprint $table) {
            $table->id();

            // Identificación
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();

            // Permite reutilizar estados en distintos módulos
            $table->string('tabla_referencia', 100)->nullable()->index();

            // Campos de workflow (desde el inicio)
            $table->integer('orden')->default(0);
            $table->boolean('es_final')->default(false);
            $table->boolean('genera_tarea')->default(false);
            $table->boolean('genera_notificacion')->default(false);

            $table->timestamps();

            // Evita duplicados por módulo
            $table->unique(['nombre', 'tabla_referencia'], 'uq_estados_nombre_tabla');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estados');
    }
};