<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProveedoresTable extends Migration
{
    public function up()
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->string('rutproveedor', 10)->primary(); // Clave primaria
            $table->string('nombre');
            $table->string('correo')->nullable();
            $table->string('direccion')->nullable();
            $table->string('contacto')->nullable();
            $table->string('imagen')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('proveedores');
    }
}