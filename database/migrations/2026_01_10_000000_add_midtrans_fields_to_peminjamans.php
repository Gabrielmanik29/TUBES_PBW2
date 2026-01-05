<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Menambahkan field untuk tracking pembayaran denda via Midtrans
     */
    public function up(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            // Snap token untuk tracking pembayaran denda
            $table->string('snap_token_denda')->nullable()->after('denda_dibayar');

            // Status pembayaran denda
            $table->enum('denda_payment_status', ['pending', 'paid', 'failed'])->default('pending')->after('snap_token_denda');

            // Waktu pembayaran berhasil
            $table->timestamp('denda_paid_at')->nullable()->after('denda_payment_status');

            // Midtrans order ID untuk referensi
            $table->string('denda_order_id')->nullable()->after('denda_paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->dropColumn([
                'snap_token_denda',
                'denda_payment_status',
                'denda_paid_at',
                'denda_order_id',
            ]);
        });
    }
};

