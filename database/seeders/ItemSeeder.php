<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // Create categories first
        $categories = [
            ['name' => 'Elektronik', 'description' => 'Barang elektronik'],
            ['name' => 'Buku', 'description' => 'Buku dan materi pembelajaran'],
            ['name' => 'Alat Tulis', 'description' => 'Alat tulis dan kantor'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create items
        $items = [
            [
                'name' => 'Laptop',
                'description' => 'Laptop untuk peminjaman',
                'stock' => 5,
                'category_id' => 1,
            ],
            [
                'name' => 'Proyektor',
                'description' => 'Proyektor untuk presentasi',
                'stock' => 3,
                'category_id' => 1,
            ],
            [
                'name' => 'Buku Algoritma',
                'description' => 'Buku tentang algoritma',
                'stock' => 10,
                'category_id' => 2,
            ],
            [
                'name' => 'Pulpen',
                'description' => 'Pulpen hitam',
                'stock' => 50,
                'category_id' => 3,
            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}
