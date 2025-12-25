<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with('category');

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Search by name or description
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $items = $query->paginate(12);
        $categories = Category::all();

        return view('items.index', compact('items', 'categories'));
    }

    public function show(Item $item)
    {
        $item->load('category');
        return view('items.show', compact('item'));
    }

    public function exportPdf()
    {
        $items = Item::with('category')->get();

        $pdf = Pdf::loadView('items.pdf', compact('items'));

        return $pdf->download('daftar-barang.pdf');
    }
}
