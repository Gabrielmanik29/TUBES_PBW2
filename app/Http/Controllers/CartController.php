<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $carts = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get();

        $totalItem = $carts->sum('quantity');
        $totalPrice = $carts->sum('total_price');

        return view('cart.index', compact('carts', 'totalItem', 'totalPrice'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $totalPrice = $product->price * $request->quantity;

        // Check if product already in cart
        $existingCart = Cart::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingCart) {
            // Update existing cart item
            $newQuantity = $existingCart->quantity + $request->quantity;
            $existingCart->update([
                'quantity' => $newQuantity,
                'total_price' => $product->price * $newQuantity,
            ]);

            return redirect()->back()->with('success', 'Keranjang berhasil diperbarui!');
        }

        // Create new cart item
        Cart::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'total_price' => $totalPrice,
        ]);

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::where('user_id', Auth::id())->findOrFail($id);
        $product = Product::findOrFail($cart->product_id);

        $cart->update([
            'quantity' => $request->quantity,
            'total_price' => $product->price * $request->quantity,
        ]);

        return redirect()->route('cart.index')->with('success', 'Keranjang berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $cart = Cart::where('user_id', Auth::id())->findOrFail($id);
        $cart->delete();

        return redirect()->route('cart.index')->with('success', 'Item berhasil dihapus dari keranjang!');
    }

    /**
     * Process checkout - move cart data to orders table.
     */
    public function checkout()
    {
        $carts = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get();

        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong!');
        }

        try {
            DB::beginTransaction();

            // Calculate total amount
            $totalAmount = $carts->sum('total_price');

            // Generate order_id format "ORD-" + random number
            $orderId = 'ORD-' . strtoupper(Str::random(6));

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_id' => $orderId,
                'total_amount' => $totalAmount,
                'status' => 'completed',
            ]);

            // Create order items
            foreach ($carts as $cart) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'quantity' => $cart->quantity,
                    'price' => $cart->product->price,
                    'total_price' => $cart->total_price,
                ]);
            }

            // Clear the cart
            Cart::where('user_id', Auth::id())->delete();

            DB::commit();

            return redirect()->route('orders.show', $order->id)->with('success', "Checkout berhasil! Silakan lakukan pembayaran.");

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('cart.index')->with('error', 'Terjadi kesalahan saat checkout: ' . $e->getMessage());
        }
    }
}

