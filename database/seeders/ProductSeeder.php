<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Product 1
        Product::create([
            'name' => 'Produk 1',
            'description' => 'Deskripsi untuk Produk 1 yang berkualitas tinggi',
            'price' => 100000,
        ]);

        // Product 2
        Product::create([
            'name' => 'Produk 2',
            'description' => 'Deskripsi untuk Produk 2 dengan harga terjangkau',
            'price' => 150000,
        ]);

        // Product 3
        Product::create([
            'name' => 'Produk 3',
            'description' => 'Deskripsi untuk Produk 3 premium edition',
            'price' => 200000,
        ]);

        // Additional products for more variety
        Product::create([
            'name' => 'Paket Hemat',
            'description' => 'Paket bundle produk dengan harga spesial',
            'price' => 500000,
        ]);

        Product::create([
            'name' => 'Produk Unggulan',
            'description' => 'Produk paling laris dari toko kami',
            'price' => 250000,
        ]);
    }
}

