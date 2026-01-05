<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk menambahkan kolom-kolom yang diperlukan untuk Midtrans Payment Gateway.
 * 
 * Kolom yang ditambahkan:
 * - snap_token: Untuk menyimpan token pembayaran dari Midtrans
 * - payment_status: Status pembayaran (unpaid, paid, failed)
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Menambahkan kolom snap_token dan payment_status ke tabel orders
        Schema::table('orders', function (Blueprint $table) {
            // snap_token: Nullable string untuk menyimpan token dari Midtrans Snap
            // Catatan: kolom payment_status sudah ada dari migration sebelumnya
            $table->string('snap_token')->nullable()->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Menghapus kolom yang ditambahkan jika migration di-rollback
            $table->dropColumn(['snap_token']);
        });
    }
};

