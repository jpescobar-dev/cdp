<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('expediente_historial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expedientes_presupuestarios')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('estado_id')->constrained('estados')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('usuario_rut', 12);
            $table->text('comentario')->nullable();
            $table->timestamp('fecha_cambio');
            $table->timestamps();

            $table->foreign('usuario_rut')->references('rut')->on('funcionarios')->restrictOnDelete()->cascadeOnUpdate();
            $table->index(['expediente_id', 'fecha_cambio'], 'idx_historial_expediente_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expediente_historial');
    }
};
