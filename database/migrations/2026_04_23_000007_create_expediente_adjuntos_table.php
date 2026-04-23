<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('expediente_adjuntos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expedientes_presupuestarios')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('nombre_archivo');
            $table->string('ruta');
            $table->string('tipo', 50)->nullable()->index();
            $table->string('subido_por', 12);
            $table->timestamps();

            $table->foreign('subido_por')->references('rut')->on('funcionarios')->restrictOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expediente_adjuntos');
    }
};
