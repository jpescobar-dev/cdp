<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cdp_borradores', function (Blueprint $table) {
            $table->date('fecha_paridad')->nullable()->after('moneda_compra');
            $table->decimal('valor_paridad', 10, 4)->nullable()->after('fecha_paridad');
        });
    }

    public function down(): void
    {
        Schema::table('cdp_borradores', function (Blueprint $table) {
            $table->dropColumn(['fecha_paridad', 'valor_paridad']);
        });
    }
};
