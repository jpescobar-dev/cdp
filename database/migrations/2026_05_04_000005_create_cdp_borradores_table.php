<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cdp_borradores', function (Blueprint $table) {
            $table->id();

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

            $table->foreignId('cdp_id')
                ->nullable()
                ->constrained('cdps')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->string('numero_cdp')->nullable();
            $table->date('fecha_emision')->nullable();
            $table->string('cf', 10)->nullable();
            $table->string('st', 10)->nullable();
            $table->boolean('gasto_operacional')->default(true);
            $table->text('nombre_iniciativa')->nullable();
            $table->string('codigo_iniciativa')->nullable();
            $table->string('cuenta_presupuestaria', 30)->nullable();
            $table->text('denominacion')->nullable();
            $table->string('unidad_ejecutora')->nullable();
            $table->string('numero_ue', 20)->nullable();
            $table->decimal('monto_impto_incluido', 20, 2)->nullable();
            $table->string('validez')->nullable();
            $table->string('caracter_gasto')->nullable();
            $table->string('medio_solicitud')->default('Requerimiento');
            $table->string('numero_requerimiento', 50)->nullable();
            $table->string('moneda_compra', 20)->nullable();
            $table->decimal('total_moneda_compra', 20, 4)->nullable();
            $table->longText('texto_certificacion')->nullable();
            $table->json('notas')->nullable();
            $table->longText('respuesta_mesa_ayuda_borrador')->nullable();
            $table->string('estado', 50)->default('borrador')->index();
            $table->boolean('requiere_revision_usuario')->default(true);
            $table->boolean('aprobado_por_usuario')->default(false);
            $table->string('aprobado_por', 12)->nullable();
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->json('json_generado')->nullable();
            $table->json('datos_faltantes')->nullable();
            $table->json('advertencias')->nullable();
            $table->string('archivo_word')->nullable();
            $table->string('archivo_pdf')->nullable();
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
        Schema::dropIfExists('cdp_borradores');
    }
};
