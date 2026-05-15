<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cdp_borradores', function (Blueprint $table) {
            $table->string('ccosto_requirente')->nullable()->after('numero_requerimiento');
        });
    }

    public function down(): void
    {
        Schema::table('cdp_borradores', function (Blueprint $table) {
            $table->dropColumn('ccosto_requirente');
        });
    }
};
