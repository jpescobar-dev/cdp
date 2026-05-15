<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agente_interacciones', function (Blueprint $table) {
            $table->id();
            $table->string('agente', 100)->index();
            $table->string('modelo', 100)->nullable();

            $table->foreignId('mesa_ayuda_requerimiento_id')
                ->nullable()
                ->constrained('mesa_ayuda_requerimientos')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('cdp_borrador_id')
                ->nullable()
                ->constrained('cdp_borradores')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->longText('prompt')->nullable();
            $table->longText('respuesta')->nullable();
            $table->json('entrada_json')->nullable();
            $table->json('salida_json')->nullable();
            $table->string('estado', 50)->default('procesado')->index();
            $table->text('error')->nullable();
            $table->integer('tokens_entrada')->nullable();
            $table->integer('tokens_salida')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agente_interacciones');
    }
};
