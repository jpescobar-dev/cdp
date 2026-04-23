<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transiciones_estados', function (Blueprint $table) {
            $table->id();

            $table->foreignId('estado_origen_id')
                ->constrained('estados')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreignId('estado_destino_id')
                ->constrained('estados')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->string('tabla_referencia', 100)->index();

            $table->string('rol_permitido', 100)->nullable();

            $table->boolean('requiere_comentario')->default(false);
            $table->boolean('genera_tarea')->default(false);
            $table->boolean('genera_notificacion')->default(false);
            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->unique(
                ['estado_origen_id', 'estado_destino_id', 'tabla_referencia', 'rol_permitido'],
                'uq_transicion_estado_rol'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transiciones_estados');
    }
};