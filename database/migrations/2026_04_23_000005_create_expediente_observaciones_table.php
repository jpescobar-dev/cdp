<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('expediente_observaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expedientes_presupuestarios')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('usuario_rut', 12);
            $table->text('observacion');
            $table->boolean('resuelta')->default(false)->index();
            $table->timestamps();

            $table->foreign('usuario_rut')->references('rut')->on('funcionarios')->restrictOnDelete()->cascadeOnUpdate();
            $table->index(['expediente_id', 'resuelta'], 'idx_observaciones_expediente_resuelta');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expediente_observaciones');
    }
};
