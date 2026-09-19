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
        DB::statement('ALTER TABLE products MODIFY stok DECIMAL(10,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE transaction_details MODIFY jumlah DECIMAL(10,2) NOT NULL');
        DB::statement('ALTER TABLE stock_histories MODIFY qty DECIMAL(10,2) NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE products MODIFY stok INT NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE transaction_details MODIFY jumlah INT NOT NULL');
        DB::statement('ALTER TABLE stock_histories MODIFY qty INT NOT NULL');
    }
};
