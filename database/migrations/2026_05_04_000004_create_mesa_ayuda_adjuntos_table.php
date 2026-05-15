<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesa_ayuda_adjuntos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mesa_ayuda_requerimiento_id')
                ->constrained('mesa_ayuda_requerimientos')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->string('nombre_archivo');
            $table->string('ruta_local');
            $table->string('url_origen')->nullable();
            $table->string('tipo_mime', 100)->nullable();
            $table->unsignedBigInteger('tamano_bytes')->nullable();
            $table->string('hash_archivo', 128)->nullable();
            $table->boolean('descargado')->default(false);
            $table->longText('texto_extraido')->nullable();
            $table->string('clasificacion_documento', 100)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['mesa_ayuda_requerimiento_id']);
            $table->index(['clasificacion_documento']);
            $table->index(['hash_archivo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesa_ayuda_adjuntos');
    }
};
