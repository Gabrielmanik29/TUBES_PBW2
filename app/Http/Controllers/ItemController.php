<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    /**
     * Display a listing of ADMIN items
     */
    public function adminIndex(Request $request)
    {
        $query = Item::with('category')->orderBy('name');

        if ($request->has('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        $items = $query->paginate(10);
        $categories = Category::orderBy('name')->get(['id', 'name']);

        return view('admin.items.index', compact('items', 'categories'));
    }

    /**
     * Display a listing of the items for users with advanced filtering
     */
    public function index(Request $request)
    {
        try {

            // ============ 1. VALIDASI & SANITASI INPUT ============
            $validator = Validator::make($request->all(), [
                'search' => 'nullable|string|max:100',
                'category' => 'nullable|exists:categories,id',
                'sort' => 'nullable|in:name,created_at,stock,popular',
                'order' => 'nullable|in:asc,desc',
                'min_stock' => 'nullable|integer|min:0',
                'max_stock' => 'nullable|integer|min:0',
                'page' => 'nullable|integer|min:1',
                'per_page' => 'nullable|integer|in:6,12,24,48',
            ]);

            if ($validator->fails()) {
                return redirect()->route('items.index')
                    ->withErrors($validator)
                    ->withInput();
            }

            // ============ 2. AMBIL PARAMETER DARI REQUEST ============
            $search = $request->input('search');
            $category_id = $request->input('category');
            $sort = $request->input('sort', 'name');
            $order = $request->input('order', 'asc');
            $min_stock = $request->input('min_stock');
            $max_stock = $request->input('max_stock');
            $page = $request->input('page', 1);

            // ============ 3. BUILD QUERY DENGAN OPTIMIZATION ============
            $query = Item::with([
                'category' => function ($q) {
                    $q->select('id', 'name', 'slug');
                },
                'peminjamans' => function ($q) {
                    $q->whereIn('status', ['diajukan', 'disetujui'])
                        ->select('id', 'item_id', 'quantity', 'status');
                }
            ])
                ->select('id', 'name', 'description', 'stock', 'category_id', 'photo', 'created_at', 'updated_at')
                ->where('stock', '>', 0);

            // ============ 4. APPLY FILTER BERDASARKAN KATEGORI ============
            if ($category_id && $category_id != 'all') {
                $query->where('category_id', $category_id);
            }

            // ============ 5. APPLY SEARCH (MULTI-FIELD) ============
            if ($search) {
                $searchTerms = explode(' ', $search);

                $query->where(function ($q) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        if (strlen($term) >= 2) { // Minimum 2 karakter untuk search
                            $q->where(function ($subQ) use ($term) {
                                $subQ->where('name', 'LIKE', "%{$term}%")
                                    ->orWhere('description', 'LIKE', "%{$term}%")
                                    ->orWhereHas('category', function ($catQ) use ($term) {
                                        $catQ->where('name', 'LIKE', "%{$term}%");
                                    });
                            });
                        }
                    }
                });
            }

            // ============ 6. FILTER BERDASARKAN STOK ============
            if ($min_stock !== null) {
                $query->where('stock', '>=', $min_stock);
            }

            if ($max_stock !== null) {
                $query->where('stock', '<=', $max_stock);
            }

            // ============ 7. APPLY SORTING ============
            $validSortColumns = ['name', 'created_at', 'stock'];

            if ($sort === 'popular') {
                // Sorting berdasarkan jumlah peminjaman
                $query->withCount([
                    'peminjamans' => function ($q) {
                        $q->where('status', 'disetujui');
                    }
                ])->orderBy('peminjamans_count', $order);
            } elseif (in_array($sort, $validSortColumns)) {
                $query->orderBy($sort, $order);
            } else {
                $query->orderBy('name', 'asc');
            }

            // ============ 8. PAGINATION DENGAN OPTIMASI ============
            $perPage = $request->input('per_page', 12); // Support custom per_page parameter
            $items = $query->paginate($perPage, ['*'], 'page', $page);
            $items->appends($request->except('page')); // Pertahankan filter di pagination

            // ============ 9. HITUNG DATA STATISTIK ============
            $stats = [
                'total_items' => Item::count(),
                'available_items' => Item::where('stock', '>', 0)->count(),
                'total_categories' => Category::count(),
                'total_borrowings' => Peminjaman::count(),
            ];

            // ============ 10. AMBIL DATA UNTUK FILTER ============
            $categories = Category::orderBy('name')->get(['id', 'name', 'slug']);

            // Items yang sedang populer (untuk sidebar)
            $popularItems = Item::with('category')
                ->where('stock', '>', 0)
                ->withCount([
                    'peminjamans' => function ($query) {
                        $query->where('status', 'disetujui');
                    }
                ])
                ->orderBy('peminjamans_count', 'desc')
                ->take(5)
                ->get();

            // ============ 11. PREPARE DATA UNTUK VIEW ============
            $filterData = [
                'search' => $search,
                'category_id' => $category_id,
                'sort' => $sort,
                'order' => $order,
                'min_stock' => $min_stock,
                'max_stock' => $max_stock,
            ];

            // ============ 11.1. PREPARE PAGE SIZE OPTIONS UNTUK PAGINATION ============
            $pageSizeOptions = [6, 12, 24, 48];

            // ============ 12. RETURN VIEW DENGAN SEMUA DATA ============
            return view('items.index', compact(
                'items',
                'categories',
                'popularItems',
                'stats',
                'filterData',
                'pageSizeOptions'
            ));

        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('ItemController@index Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            // Fallback: tampilkan items tanpa filter jika ada error
            $items = Item::with('category')
                ->select('id', 'name', 'description', 'stock', 'category_id', 'photo', 'created_at', 'updated_at')
                ->where('stock', '>', 0)
                ->orderBy('name')
                ->paginate(12);

            $categories = Category::orderBy('name')->get();
            $popularItems = collect([]);
            $stats = [
                'total_items' => Item::count(),
                'available_items' => Item::where('stock', '>', 0)->count(),
                'total_categories' => Category::count(),
                'total_borrowings' => Peminjaman::count(),
            ];
            $pageSizeOptions = [6, 12, 24, 48];

            // Default filter data untuk fallback
            $filterData = [
                'search' => '',
                'category_id' => 'all',
                'sort' => 'name',
                'order' => 'asc',
                'min_stock' => null,
                'max_stock' => null,
            ];

            return view('items.index', compact(
                'items',
                'categories',
                'popularItems',
                'stats',
                'filterData',
                'pageSizeOptions'
            ))->with('error', 'Terjadi kesalahan saat memuat data. Silakan coba lagi.');
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            // Pastikan folder items ada di storage/app/public
            if (!Storage::disk('public')->exists('items')) {
                Storage::disk('public')->makeDirectory('items');
            }
            $photoPath = $request->file('photo')->store('items', 'public');
        }

        Item::create([
            'name' => $data['name'],
            'category_id' => $data['category_id'],
            'stock' => $data['stock'],
            'description' => $data['description'] ?? null,
            'photo' => $photoPath,
        ]);

        return redirect()->route('admin.items.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function update(Request $request, Item $item)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $photoPath = $item->photo; // Keep existing photo by default

        if ($request->hasFile('photo')) {
            // Ensure items folder exists
            if (!Storage::disk('public')->exists('items')) {
                Storage::disk('public')->makeDirectory('items');
            }
            // Delete old photo if exists
            if ($item->photo && Storage::disk('public')->exists($item->photo)) {
                Storage::disk('public')->delete($item->photo);
            }
            // Store new photo
            $photoPath = $request->file('photo')->store('items', 'public');
        }

        $item->update([
            'name' => $data['name'],
            'category_id' => $data['category_id'],
            'stock' => $data['stock'],
            'description' => $data['description'] ?? null,
            'photo' => $photoPath,
        ]);

        return redirect()->route('admin.items.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.items.create', compact('categories'));
    }

    public function edit(Item $item)
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.items.edit', compact('item', 'categories'));
    }

    public function destroy(Item $item)
    {
        // Delete photo if exists
        if ($item->photo && Storage::disk('public')->exists($item->photo)) {
            Storage::disk('public')->delete($item->photo);
        }

        $item->delete();

        return redirect()->route('admin.items.index')->with('success', 'Barang berhasil dihapus.');
    }

    /**
     * Display the specified item with detailed information
     */
    public function show(Request $request, Item $item)
    {
        try {
            // ============ 1. VALIDASI AKSES ============
            if (!$item) {
                abort(404, 'Barang tidak ditemukan');
            }

            // ============ 2. LOAD DATA DENGAN EAGER LOADING ============
            $item->load([
                'category' => function ($q) {
                    $q->select('id', 'name', 'description');
                },
                'peminjamans' => function ($q) {
                    $q->with([
                        'user' => function ($userQ) {
                            $userQ->select('id', 'name', 'email');
                        }
                    ])
                        ->whereIn('status', ['disetujui', 'dikembalikan'])
                        ->orderBy('created_at', 'desc')
                        ->limit(10);
                }
            ]);

            // ============ 3. HITUNG STOK TERSEBUT ============
            $stokDipinjam = $item->peminjamans()
                ->whereIn('status', ['diajukan', 'disetujui'])
                ->sum('quantity');

            $stokTersedia = max(0, $item->stock - $stokDipinjam);

            // ============ 4. AMBIL ITEM TERKAIT ============
            $relatedItems = Item::with('category')
                ->where('category_id', $item->category_id)
                ->where('id', '!=', $item->id)
                ->where('stock', '>', 0)
                ->inRandomOrder()
                ->limit(4)
                ->get();

            // ============ 5. STATISTIK ITEM ============
            $itemStats = [
                'total_borrowings' => $item->peminjamans()->count(),
                'active_borrowings' => $item->peminjamans()
                    ->whereIn('status', ['diajukan', 'disetujui'])
                    ->count(),
                'returned_borrowings' => $item->peminjamans()
                    ->where('status', 'dikembalikan')
                    ->count(),
            ];

            // ============ 6. CHECK USER'S BORROWING HISTORY ============
            $userBorrowingHistory = null;
            if (Auth::check()) {
                $userBorrowingHistory = $item->peminjamans()
                    ->where('user_id', Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            // ============ 7. RETURN VIEW ============
            return view('items.show', compact(
                'item',
                'stokTersedia',
                'relatedItems',
                'itemStats',
                'userBorrowingHistory'
            ));

        } catch (\Exception $e) {
            \Log::error('ItemController@show Error: ' . $e->getMessage(), [
                'item_id' => $item->id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('items.index')
                ->with('error', 'Terjadi kesalahan saat memuat detail barang.');
        }
    }

    /**
     * API endpoint for AJAX search (autocomplete)
     */
    public function searchAjax(Request $request)
    {
        try {
            $query = $request->input('q');

            if (strlen($query) < 2) {
                return response()->json([]);
            }

            $items = Item::with('category')
                ->where('stock', '>', 0)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                        ->orWhere('description', 'LIKE', "%{$query}%")
                        ->orWhereHas('category', function ($catQ) use ($query) {
                            $catQ->where('name', 'LIKE', "%{$query}%");
                        });
                })
                ->orderBy('name')
                ->limit(10)
                ->get(['id', 'name', 'category_id', 'stock']);

            $results = $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'category' => $item->category->name,
                    'stock' => $item->stock,
                    'url' => route('items.show', $item),
                ];
            });

            return response()->json($results);

        } catch (\Exception $e) {
            \Log::error('ItemController@searchAjax Error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    /**
     * Get available dates for borrowing (API)
     */
    public function getAvailableDates(Item $item)
    {
        try {
            // Cari tanggal-tanggal yang sudah banyak peminjaman
            $busyDates = Peminjaman::where('item_id', $item->id)
                ->whereIn('status', ['diajukan', 'disetujui'])
                ->where('tanggal_pinjam', '>=', now())
                ->groupBy('tanggal_pinjam')
                ->havingRaw('SUM(quantity) >= ?', [$item->stock * 0.8]) // 80% stok terpakai
                ->pluck('tanggal_pinjam')
                ->map(function ($date) {
                    return $date->format('Y-m-d');
                })
                ->toArray();

            return response()->json([
                'busy_dates' => $busyDates,
                'max_quantity' => $item->stock
            ]);

        } catch (\Exception $e) {
            \Log::error('ItemController@getAvailableDates Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Export items to PDF (Bonus feature)
     */
    public function exportPdf(Request $request)
    {
        try {
            // Filter items
            $items = Item::with('category')
                ->where('stock', '>', 0)
                ->orderBy('name')
                ->get();

            // In a real project, you would use a PDF library like DomPDF
            // For now, we'll just return a view
            return view('items.export-pdf', compact('items'));

        } catch (\Exception $e) {
            \Log::error('ItemController@exportPdf Error: ' . $e->getMessage());
            return redirect()->route('items.index')
                ->with('error', 'Gagal mengekspor data ke PDF.');
        }
    }

    /**
     * Calculate estimated return date
     */
    public function calculateReturnDate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_pinjam' => 'required|date|after_or_equal:today',
            'durasi' => 'required|integer|min:1|max:30' // maksimal 30 hari
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ], 400);
        }

        $tanggalPinjam = \Carbon\Carbon::parse($request->tanggal_pinjam);
        $durasi = $request->durasi;

        // Tambahkan hari kerja (exclude weekends)
        $tanggalKembali = $tanggalPinjam->copy();
        $daysAdded = 0;

        while ($daysAdded < $durasi) {
            $tanggalKembali->addDay();
            // Skip weekend (Sabtu=6, Minggu=7)
            if (!in_array($tanggalKembali->dayOfWeek, [6, 7])) {
                $daysAdded++;
            }
        }

        return response()->json([
            'tanggal_kembali' => $tanggalKembali->format('Y-m-d'),
            'formatted' => $tanggalKembali->translatedFormat('d F Y'),
            'warning' => $durasi > 14 ? 'Durasi peminjaman melebihi 2 minggu' : null
        ]);
    }

    /**
     * Check if item is available for borrow
     */
    public function isAvailableForBorrow(Item $item, int $quantity): bool
    {
        $dipinjam = Peminjaman::where('item_id', $item->id)
            ->whereIn('status', ['diajukan', 'disetujui'])
            ->sum('quantity');

        $stokTersedia = (int) $item->stock - (int) $dipinjam;

        return $stokTersedia >= $quantity;
    }
}

