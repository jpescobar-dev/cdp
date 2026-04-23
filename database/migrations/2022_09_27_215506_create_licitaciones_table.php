<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLicitacionesTable extends Migration
{
    public function up()
    {
        Schema::create('licitaciones', function (Blueprint $table) {
            $table->string('numero_licitacion')->primary(); // PK personalizada

            // Campos principales (sin prefijos redundantes)
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('estado')->nullable()->index();
            $table->string('tipo')->nullable()->index();
            $table->string('unidad_compra')->nullable()->index();

            // Datos financieros y métricas
            $table->decimal('monto_total_estimado', 15, 2)->nullable();
            $table->integer('numero_ofertas_recibidas')->nullable();

            // Fechas relevantes
            $table->timestamp('fecha_publicacion')->nullable();
            $table->timestamp('fecha_adjudicacion')->nullable();

            // Control de registros
            $table->timestamps();
            $table->softDeletes(); // Permite borrado lógico
        });
    }

    public function down()
    {
        Schema::dropIfExists('licitaciones');
    }
}
