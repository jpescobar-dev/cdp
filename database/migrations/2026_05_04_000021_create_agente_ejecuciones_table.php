<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agente_ejecuciones', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->string('agente_codigo', 100)->index();
            $table->string('agente_nombre', 150)->nullable();
            $table->string('tipo_tarea', 100)->index();
            $table->string('estado', 50)->default('pendiente')->index();

            $table->foreignId('solicitado_por_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('agente_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('mesa_ayuda_requerimiento_id')
                ->nullable()
                ->constrained('mesa_ayuda_requerimientos')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('expediente_presupuestario_id')
                ->nullable()
                ->constrained('expedientes_presupuestarios')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('cdp_borrador_id')
                ->nullable()
                ->constrained('cdp_borradores')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->json('input_json')->nullable();
            $table->json('output_json')->nullable();

            $table->text('resumen')->nullable();
            $table->text('error_mensaje')->nullable();
            $table->string('error_tipo', 150)->nullable();
            $table->longText('stack_trace')->nullable();

            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_termino')->nullable();
            $table->unsignedInteger('duracion_ms')->nullable();

            $table->unsignedSmallInteger('intentos')->default(0);
            $table->timestamp('proximo_reintento')->nullable();

            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['agente_codigo', 'estado']);
            $table->index(['mesa_ayuda_requerimiento_id', 'estado'], 'idx_agente_req_estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agente_ejecuciones');
    }
};
