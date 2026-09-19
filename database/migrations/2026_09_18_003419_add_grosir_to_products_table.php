<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('harga_grosir', 15, 2)->nullable()->default(0)->after('harga_jual');
            $table->integer('minimal_grosir')->nullable()->default(0)->after('harga_grosir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['harga_grosir', 'minimal_grosir']);
        });
    }
};
