<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the test user
        $user = User::where('email', 'user@example.com')->first();

        if (!$user) {
            $this->command->error('User dengan email user@example.com tidak ditemukan! Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        // Get all products
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->error('Tidak ada produk ditemukan! Jalankan ProductSeeder terlebih dahulu.');
            return;
        }

        // Add products to cart automatically
        $cartItems = [
            [
                'product_id' => 1,
                'quantity' => 2,
            ],
            [
                'product_id' => 2,
                'quantity' => 1,
            ],
            [
                'product_id' => 3,
                'quantity' => 3,
            ],
        ];

        foreach ($cartItems as $item) {
            $product = Product::find($item['product_id']);

            // Check if product already in cart
            $existingCart = Cart::where('user_id', $user->id)
                ->where('product_id', $item['product_id'])
                ->first();

            if (!$existingCart) {
                Cart::create([
                    'user_id' => $user->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'total_price' => $product->price * $item['quantity'],
                ]);

                $this->command->info("Produk '{$product->name}' ditambahkan ke keranjang user.");
            } else {
                $this->command->info("Produk '{$product->name}' sudah ada di keranjang user.");
            }
        }

        // Display cart summary
        $cartTotal = Cart::where('user_id', $user->id)->sum('total_price');
        $cartCount = Cart::where('user_id', $user->id)->count();

        $this->command->info("Total item di keranjang: {$cartCount}");
        $this->command->info("Total harga: Rp " . number_format($cartTotal, 0, ',', '.'));
    }
}

