<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenescomprasTable extends Migration

{
    public function up()
    {
        Schema::create('ordenescompras', function (Blueprint $table) {
            $table->string('orden_compra',13)->primary();
            $table->string('nombre');
            $table->string('tipo');
            $table->string('estado')->nullable();
            $table->string('unidad_compra')->default('2182');
            $table->string('proveedor')->nullable(); // Nombre descriptivo
            $table->string('rutproveedor')->nullable(); // Clave del proveedor

            $table->date('fecha_creacion')->nullable();
            $table->date('fecha_envio')->nullable();

            $table->decimal('monto_neto', 15, 2)->nullable();
            $table->decimal('descuentos', 15, 2)->nullable();
            $table->decimal('cargos', 15, 2)->nullable();
            $table->decimal('iva', 15, 2)->nullable();
            $table->decimal('impuesto_especifico', 15, 2)->nullable();
            $table->decimal('total', 15, 2)->nullable();

            $table->timestamps();

            // Relación con proveedores
            $table->foreign('rutproveedor')
                  ->references('rutproveedor')
                  ->on('proveedores')
                  ->nullOnDelete(); // o ->cascadeOnDelete() si querís eliminar en cascada
        });
    }

    public function down()
    {
        Schema::dropIfExists('ordenescompras');
    }
}
