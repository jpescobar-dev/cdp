<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('catalogos', function (Blueprint $table) {
            $table->string('catalogo', 10)->primary(); 
            $table->string('nombre', 100);
            $table->longText('descripcion');        
            $table->enum('estado', ['Activo', 'Inactivo']);
            $table->string('item', 10); // Mismo largo que en la tabla items

            $table->timestamps();

            // Clave foránea hacia items
            $table->foreign('item')
                  ->references('item')
                  ->on('items')
                  ->onUpdate('cascade')
                  ->onDelete('restrict'); // O 'set null' si permitís valores nulos
        });
    }

    public function down()
    {
        Schema::dropIfExists('catalogos');
    }
};
