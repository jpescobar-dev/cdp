<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expedientes_presupuestarios', function (Blueprint $table) {
            $table->dropForeign(['ccosto']);
            $table->dropForeign(['cfinanciero']);
            $table->string('ccosto', 10)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('expedientes_presupuestarios', function (Blueprint $table) {
            $table->string('ccosto', 10)->nullable(false)->change();
            $table->foreign('ccosto')->references('ccosto')->on('ccostos')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('cfinanciero')->references('cfinanciero')->on('cfinancieros')->onUpdate('cascade')->onDelete('restrict');
        });
    }
};
