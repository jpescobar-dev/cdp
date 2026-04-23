<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up()
    {
        Schema::create('asignaciones', function (Blueprint $table) {         
            $table->string('asignacion')->primary();  
            $table->string('item')  ;
            $table->string('nombre');                  
            $table->longText('descripcion')->nullable();  
            $table->foreign('item')
                  ->references('item')
                  ->on('items')
                  ->onUpdate('cascade')
                  ->onDelete('restrict'); // O 'set null' si permitís valores nulos
       

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignaciones');
    }
};
