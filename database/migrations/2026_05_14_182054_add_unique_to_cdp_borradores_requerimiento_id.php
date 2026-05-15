<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cdp_borradores', function (Blueprint $table) {
            $table->unique('mesa_ayuda_requerimiento_id', 'cdp_borradores_requerimiento_unique');
        });
    }

    public function down(): void
    {
        Schema::table('cdp_borradores', function (Blueprint $table) {
            $table->dropUnique('cdp_borradores_requerimiento_unique');
        });
    }
};
