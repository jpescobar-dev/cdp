<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();
            $table->string('proyecto');
            $table->text('descripcion');
            $table->string('codigo')->unique();
            $table->date('fecha_inicio');
            $table->date('fecha_termino');
            $table->integer('avance')->default(0); // Porcentaje de avance
            $table->decimal('monto_estimado', 15, 2);
            $table->decimal('monto_asignado', 15, 2);
            $table->string('cfinanciero_id');
            $table->unsignedBigInteger('estado_id');
            $table->timestamps();

            // Llaves foráneas
          //  $table->foreign('cfinanciero_id')->references('id_cfinanciero')->on('cfinancieros')->onDelete('cascade');
            $table->foreign('estado_id')->references('id')->on('estados')->onDelete('cascade');
        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('proyectos');
    }
};
