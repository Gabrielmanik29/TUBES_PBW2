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
        Schema::table('order_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('order_payments', 'midtrans_order_id')) {
                $table->string('midtrans_order_id')->nullable()->after('snap_token');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_payments', function (Blueprint $table) {
            if (Schema::hasColumn('order_payments', 'midtrans_order_id')) {
                $table->dropColumn('midtrans_order_id');
            }
        });
    }
};
