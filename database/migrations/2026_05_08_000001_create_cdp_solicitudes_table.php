<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cdp_solicitudes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('nombre_requirente');
            $table->string('rut_requirente', 12);
            $table->string('unidad_requirente');

            $table->text('glosa');
            $table->string('proveedor');

            $table->decimal('monto_estimado', 20, 2);
            $table->enum('moneda', ['CLP', 'UF'])->default('CLP');

            $table->json('documentos')->nullable();

            $table->string('estado', 30)->default('borrador')->index();
            $table->string('pdf_path')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cdp_solicitudes');
    }
};
