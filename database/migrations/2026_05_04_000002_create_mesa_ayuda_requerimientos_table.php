<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesa_ayuda_requerimientos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('extraccion_id')
                ->nullable()
                ->constrained('mesa_ayuda_extracciones')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->string('folio', 50)->unique();
            $table->timestamp('fecha_hora')->nullable();
            $table->string('estado_externo', 100)->nullable();

            $table->string('componente')->nullable();
            $table->string('tipo_requerimiento')->nullable();
            $table->string('tribunal')->nullable();
            $table->string('solicitado_por')->nullable();
            $table->string('solicitado_para')->nullable();
            $table->string('tiempo_estimado_solucion')->nullable();

            $table->longText('observacion_principal')->nullable();
            $table->text('tipificacion')->nullable();
            $table->string('url_detalle')->nullable();

            $table->string('clasificacion', 100)->nullable();
            $table->boolean('requiere_cdp')->default(false);
            $table->string('confianza_clasificacion', 30)->nullable();
            $table->integer('score_clasificacion')->default(0);
            $table->json('evidencias_clasificacion')->nullable();

            $table->string('destino_flujo', 100)->nullable();
            $table->boolean('procesar_automaticamente')->default(false);
            $table->text('motivo_routing')->nullable();

            $table->foreignId('estado_id')
                ->nullable()
                ->constrained('estados')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('expediente_presupuestario_id')
                ->nullable()
                ->constrained('expedientes_presupuestarios')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->json('head_json')->nullable();
            $table->json('body_json')->nullable();
            $table->json('json_completo')->nullable();

            $table->timestamp('fecha_captura')->nullable();
            $table->string('origen')->default('mesa_ayuda');
            $table->text('error_captura')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['clasificacion', 'requiere_cdp']);
            $table->index(['estado_id']);
            $table->index(['fecha_hora']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesa_ayuda_requerimientos');
    }
};
