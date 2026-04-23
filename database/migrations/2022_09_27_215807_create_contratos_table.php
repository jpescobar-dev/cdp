<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContratosTable extends Migration
{
   
    public function up()
    {
        Schema::create('contratos', function (Blueprint $table) {     
            $table->string('contrato')->primary();
            $table->string('licitacion');          
            $table->string('estado')->default('5');
            $table->string('tipo');
            $table->string('referencia');
            $table->date('fechainicio');
            $table->date('fechavencimiento');
            $table->string('razonsocial');
            $table->string('rut');
            $table->string('materia');
            $table->string('submateria');         
        

            $table->timestamps();
        });
    }

   
    public function down()
    {
        Schema::dropIfExists('contratos');
    }
}
