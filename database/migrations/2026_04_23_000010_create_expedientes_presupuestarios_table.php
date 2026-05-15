<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expedientes_presupuestarios', function (Blueprint $table) {
            $table->id();
            $table->string('correlativo')->unique();
            $table->integer('anio');
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
            $table->string('numero_requerimiento', 50)->nullable();
            $table->foreignId('estado_id')->constrained('estados')->onUpdate('cascade')->onDelete('restrict');
            $table->timestamp('fecha_ingreso')->nullable();
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->timestamp('fecha_emision')->nullable();
            $table->string('numero_cdp')->nullable();
            $table->string('archivo_pdf')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('solicitante_rut')->references('rut')->on('funcionarios')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('responsable_rut')->references('rut')->on('funcionarios')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('ccosto')->references('ccosto')->on('ccostos')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('cfinanciero')->references('cfinanciero')->on('cfinancieros')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expedientes_presupuestarios');
    }
};
