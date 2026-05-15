<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cdp_borradores', function (Blueprint $table) {
            $table->string('tipo_gasto', 3)->default('GO')->after('gasto_operacional');
        });
    }

    public function down(): void
    {
        Schema::table('cdp_borradores', function (Blueprint $table) {
            $table->dropColumn('tipo_gasto');
        });
    }
};
