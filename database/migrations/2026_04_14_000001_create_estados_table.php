<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->string('tabla_referencia', 100)->nullable()->index();
            $table->timestamps();

            $table->unique(['nombre', 'tabla_referencia'], 'uq_estados_nombre_tabla');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estados');
    }
};