<?php
// tests/Unit/PeminjamanTest.php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Peminjaman;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class PeminjamanTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function dapat_mengitung_hari_keterlambatan()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['stock' => 10]);
        
        $peminjaman = Peminjaman::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'tanggal_pinjam' => Carbon::now()->subDays(10),
            'tanggal_kembali' => Carbon::now()->subDays(5),
            'tanggal_pengembalian_aktual' => Carbon::now()->subDays(3),
            'status' => 'dikembalikan',
            'denda' => 0,
            'denda_dibayar' => false,
        ]);

        // Keterlambatan: dikembalikan 2 hari setelah tanggal kembali
        $this->assertEquals(2, $peminjaman->hariKeterlambatan());
    }

    /** @test */
    public function dapat_mengitung_denda_otomatis()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['stock' => 10]);
        
        $peminjaman = Peminjaman::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'quantity' => 2,
            'tanggal_pinjam' => Carbon::now()->subDays(10),
            'tanggal_kembali' => Carbon::now()->subDays(5),
            'tanggal_pengembalian_aktual' => Carbon::now()->subDays(2),
            'status' => 'dikembalikan',
            'denda' => 0,
            'denda_dibayar' => false,
        ]);

        // 3 hari terlambat × Rp 10.000 = Rp 30.000
        $this->assertEquals(30000, $peminjaman->hitungDenda());
    }

    /** @test */
    public function validasi_stok_cukup()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['stock' => 5]);
        
        $peminjaman = Peminjaman::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'quantity' => 3,
            'tanggal_pinjam' => Carbon::now(),
            'tanggal_kembali' => Carbon::now()->addDays(3),
            'status' => 'diajukan',
        ]);

        $this->assertTrue($peminjaman->stokCukup());

        $peminjaman2 = Peminjaman::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'quantity' => 10, // Melebihi stok
            'tanggal_pinjam' => Carbon::now(),
            'tanggal_kembali' => Carbon::now()->addDays(3),
            'status' => 'diajukan',
        ]);

        $this->assertFalse($peminjaman2->stokCukup());
    }

    /** @test */
    public function cek_peminjaman_terlambat()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['stock' => 10]);
        
        $peminjaman = Peminjaman::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'tanggal_pinjam' => Carbon::now()->subDays(10),
            'tanggal_kembali' => Carbon::now()->subDays(5), // Sudah lewat 5 hari
            'status' => 'disetujui',
        ]);

        $this->assertTrue($peminjaman->isTerlambat());
    }
}