<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesa_ayuda_historial', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mesa_ayuda_requerimiento_id')
                ->constrained('mesa_ayuda_requerimientos')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->date('fecha')->nullable();
            $table->time('hora')->nullable();
            $table->string('estado_externo', 100)->nullable();
            $table->string('accion', 100)->nullable();
            $table->string('usuario_externo')->nullable();
            $table->longText('observacion')->nullable();
            $table->json('raw_json')->nullable();
            $table->timestamps();

            $table->index(['mesa_ayuda_requerimiento_id']);
            $table->index(['estado_externo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesa_ayuda_historial');
    }
};
