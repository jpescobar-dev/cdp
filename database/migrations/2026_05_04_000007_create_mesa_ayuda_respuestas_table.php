<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesa_ayuda_respuestas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mesa_ayuda_requerimiento_id')
                ->constrained('mesa_ayuda_requerimientos')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('cdp_borrador_id')
                ->nullable()
                ->constrained('cdp_borradores')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->longText('texto_respuesta');
            $table->string('estado', 50)->default('borrador')->index();
            $table->string('aprobado_por', 12)->nullable();
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->timestamp('fecha_envio')->nullable();
            $table->string('folio_externo')->nullable();
            $table->json('snapshot_envio')->nullable();
            $table->text('error_envio')->nullable();
            $table->timestamps();

            $table->foreign('aprobado_por')
                ->references('rut')
                ->on('funcionarios')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesa_ayuda_respuestas');
    }
};
