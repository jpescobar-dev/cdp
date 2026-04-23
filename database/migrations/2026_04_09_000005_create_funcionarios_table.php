<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
       Schema::create('funcionarios', function (Blueprint $table) {
            $table->string('rut', 12)->primary();
            $table->string('nombres', 100);
            $table->string('apellido_paterno', 100);
            $table->string('apellido_materno', 100)->nullable();
            $table->string('nombre_completo', 220);
            $table->string('email', 150)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('cargo', 150);
            $table->string('ccosto', 10);
            $table->string('cfinanciero', 4)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('ccosto')
                ->references('ccosto')
                ->on('ccostos')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('cfinanciero')
                ->references('cfinanciero')
                ->on('cfinancieros')
                ->onUpdate('cascade')
                ->onDelete('restrict');
             $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funcionarios');
    }
};