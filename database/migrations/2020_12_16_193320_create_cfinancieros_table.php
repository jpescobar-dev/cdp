<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCfinancierosTable extends Migration
{
    public function up()
    {
        Schema::create('cfinancieros', function (Blueprint $table) {       
            $table->string('cfinanciero', 4)->primary();
            $table->string('nombre', 50);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cfinancieros');
    }
}



