<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('expedientes_presupuestarios', function (Blueprint $table) {
            $table->id();

            $table->string('correlativo')->unique();
            $table->unsignedInteger('anio')->index();

            $table->string('solicitante_rut', 12);
            $table->string('responsable_rut', 12)->nullable();

            $table->string('ccosto', 10);
            $table->string('cfinanciero', 4)->nullable();

            $table->string('cuenta_presupuestaria', 20);
            $table->string('denominacion', 255)->nullable();
            $table->decimal('monto', 15, 2);
            $table->string('moneda', 10)->default('CLP');
            $table->decimal('total_moneda_compra', 15, 2)->nullable();

            $table->text('glosa');
            $table->string('caracter_gasto', 50)->nullable();
            $table->string('medio_solicitud', 100)->nullable();
            $table->string('numero_requerimiento', 50)->nullable()->index();

            $table->foreignId('estado_id')->constrained('estados')->restrictOnDelete()->cascadeOnUpdate();
            $table->timestamp('fecha_ingreso')->nullable();
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->timestamp('fecha_emision')->nullable();

            $table->string('numero_cdp', 50)->nullable()->index();
            $table->string('archivo_pdf')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('solicitante_rut')->references('rut')->on('funcionarios')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreign('responsable_rut')->references('rut')->on('funcionarios')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('ccosto')->references('ccosto')->on('ccostos')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreign('cfinanciero')->references('cfinanciero')->on('cfinancieros')->restrictOnDelete()->cascadeOnUpdate();

            $table->index(['estado_id', 'anio'], 'idx_expedientes_estado_anio');
            $table->index(['ccosto', 'anio'], 'idx_expedientes_ccosto_anio');
            $table->index(['solicitante_rut', 'created_at'], 'idx_expedientes_solicitante_fecha');
            $table->index(['responsable_rut', 'created_at'], 'idx_expedientes_responsable_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expedientes_presupuestarios');
    }
};
