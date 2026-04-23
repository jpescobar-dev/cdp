<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cdps', function (Blueprint $table) {
            $table->id();

            $table->string('num_cdp')->unique();
            $table->date('fecha_cdp');

            $table->string('id_sgf')->nullable();

            // Relación con ccostos.ccosto (PK string)
            $table->string('ccosto', 10);
            $table->foreign('ccosto')
                ->references('ccosto')
                ->on('ccostos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('requerimiento')->nullable();
            $table->text('descripcion');

            $table->enum('moneda', ['CLP', 'UF']);
            $table->decimal('total_moneda_compra', 20, 2)->nullable();
            $table->date('fechaparidad')->nullable();
            $table->decimal('paridad', 15, 4)->nullable();

            $table->decimal('monto_total_impto_incluido', 20, 2)->nullable();
            $table->enum('st', ['22', '29', '31']);

            // CORRECCIÓN: catalogos no tiene id, su PK es catalogo (string)
            $table->string('catalogo', 10);
            $table->foreign('catalogo')
                ->references('catalogo')
                ->on('catalogos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->text('denominacion')->nullable();

            $table->enum('tipo_gasto1', ['GO', 'INI']);

            // Esto asume que proyectos.id existe como unsignedBigInteger
            $table->foreignId('proyecto_id')
                ->constrained('proyectos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->integer('pp')->default(100);
            $table->enum('tipo_gasto2', ['TRANSITORIO', 'PERMANENTE']);

            $table->boolean('cargado_cgu')->default(false);
            $table->boolean('comprometido_cgu')->default(false);

            $table->decimal('total_compromiso', 20, 2)->default(0);
            $table->unsignedBigInteger('num_compromiso')->nullable();

            $table->text('observaciones')->nullable();

            // Mejor nullable que dejar una fecha fija quemada
            $table->date('validez')->nullable();

            // Esto asume que estados.id existe como unsignedBigInteger
            $table->foreignId('estado_id')
                ->default(1)
                ->constrained('estados')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cdps');
    }
};