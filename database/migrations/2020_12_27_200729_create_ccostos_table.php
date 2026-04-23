<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCcostosTable extends Migration
{
    public function up()
    {
        Schema::create('ccostos', function (Blueprint $table) {
            $table->string('ccosto', 10)->primary(); // Puedes ajustar el largo según tus reglas
            $table->string('nombre', 150);
            $table->string('cfinanciero', 4); // Mismo largo que en la tabla cfinancieros

            $table->timestamps();

            // Definición de clave foránea
            $table->foreign('cfinanciero')
                  ->references('cfinanciero')
                  ->on('cfinancieros')
                  ->onUpdate('cascade')
                  ->onDelete('restrict'); // o 'set null' si el campo fuera nullable
        });
    }

    public function down()
    {
        Schema::dropIfExists('ccostos');
    }
}
