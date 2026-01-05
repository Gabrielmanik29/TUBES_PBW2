<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PeminjamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // database/seeders/PeminjamanSeeder.php
public function run()
{
    $users = \App\Models\User::where('role', 'user')->get();
    $items = \App\Models\Item::all();
    
    foreach(range(1, 20) as $i) {
        $user = $users->random();
        $item = $items->random();
        
        \App\Models\Peminjaman::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'quantity' => rand(1, min(3, $item->stock)),
            'tanggal_pinjam' => now()->subDays(rand(1, 30)),
            'tanggal_kembali' => now()->addDays(rand(1, 14)),
            'status' => ['diajukan', 'disetujui', 'dikembalikan', 'ditolak'][rand(0, 3)],
            'denda' => rand(0, 1) ? rand(5000, 50000) : 0,
            'denda_dibayar' => rand(0, 1),
        ]);
    }
}
}
