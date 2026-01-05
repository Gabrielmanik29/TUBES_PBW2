<<<<<<< HEAD
<!DOCTYPE html>
<html lang="id" x-data="itemFilter()" x-init="init()">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Barang - Sistem Inventaris Kampus</title>
    
    <!-- Meta Tags for SEO -->
    <meta name="description" content="Temukan dan pinjam barang inventaris kampus dengan mudah. Sistem peminjaman online terpadu.">
    <meta name="keywords" content="inventaris, peminjaman, barang kampus, alat laboratorium, peralatan">
    <meta name="author" content="Kelompok 5 D3IF-48-04">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Datepicker CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #7c3aed;
            --accent-color: #06b6d4;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 40;
            backdrop-filter: blur(10px);
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }
        
        /* Responsive grid adjustments */
        @media (max-width: 640px) {
            .mobile-stack {
                flex-direction: column;
            }
            
            .mobile-full {
                width: 100%;
            }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans">
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-white bg-opacity-90 z-50 flex items-center justify-center transition-opacity duration-300 hidden">
        <div class="text-center">
            <div class="w-16 h-16 border-4 border-t-primary border-r-transparent border-b-secondary border-l-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-gray-600 font-medium">Memuat data barang...</p>
        </div>
    </div>

    <!-- Navigation -->
    @include('layouts.navigation')

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-6 fade-in">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">
                        <span class="gradient-text">Daftar Barang</span> Inventaris
                    </h1>
                    <p class="text-gray-600">
                        Temukan dan pinjam barang yang Anda butuhkan. 
                        <span class="hidden md:inline">Sistem peminjaman online terpadu kampus.</span>
                    </p>
                </div>
                

                <!-- Quick Actions -->
                <div class="flex flex-wrap gap-3">
                    <button onclick="toggleAdvancedFilters()"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition">
                        <i class="fas fa-filter"></i>
                        <span>Filter Lanjutan</span>
                    </button>
                    
                    <a href="{{ route('items.export.pdf') }}" 
                       target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        <i class="fas fa-file-export"></i>
                        <span class="hidden md:inline">Export PDF</span>
                    </a>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Total Barang</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $stats['total_items'] ?? 0 }}</p>
                        </div>
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-boxes text-blue-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Tersedia</p>
                            <p class="text-2xl font-bold text-green-600">{{ $stats['available_items'] ?? 0 }}</p>
                        </div>
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Kategori</p>
                            <p class="text-2xl font-bold text-purple-600">{{ $stats['total_categories'] ?? 0 }}</p>
                        </div>
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-tags text-purple-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Total Peminjaman</p>
                            <p class="text-2xl font-bold text-orange-600">{{ $stats['total_borrowings'] ?? 0 }}</p>
                        </div>
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-handshake text-orange-600"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advanced Filters (Collapsible) -->
        <div id="advancedFilters" class="bg-white rounded-xl shadow-lg p-6 mb-6 transition-all duration-300 hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">Filter Lanjutan</h3>
                <button onclick="toggleAdvancedFilters()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form method="GET" action="{{ route('items.index') }}" id="advancedFilterForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Stock Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rentang Stok</label>
                        <div class="flex gap-2">
                            <input type="number" 
                                   name="min_stock" 
                                   value="{{ $filterData['min_stock'] ?? '' }}"
                                   placeholder="Min"
                                   min="0"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <span class="self-center text-gray-400">-</span>
                            <input type="number" 
                                   name="max_stock" 
                                   value="{{ $filterData['max_stock'] ?? '' }}"
                                   placeholder="Max"
                                   min="0"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <!-- Sort Options -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan Berdasarkan</label>
                        <select name="sort" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                            <option value="name" {{ ($filterData['sort'] ?? '') == 'name' ? 'selected' : '' }}>Nama A-Z</option>
                            <option value="created_at" {{ ($filterData['sort'] ?? '') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                            <option value="stock" {{ ($filterData['sort'] ?? '') == 'stock' ? 'selected' : '' }}>Stok Terbanyak</option>
                            <option value="popular" {{ ($filterData['sort'] ?? '') == 'popular' ? 'selected' : '' }}>Paling Populer</option>
                        </select>
                    </div>
                    
                    <!-- Order Direction -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Urutan</label>
                        <select name="order" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                            <option value="asc" {{ ($filterData['order'] ?? '') == 'asc' ? 'selected' : '' }}>Naik (A-Z)</option>
                            <option value="desc" {{ ($filterData['order'] ?? '') == 'desc' ? 'selected' : '' }}>Turun (Z-A)</option>
                        </select>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" 
                            onclick="resetAdvancedFilters()"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium">
                        Reset Filter
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Left Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Quick Filter Card -->
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-24">
                    <!-- Category Filter -->
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-tags text-blue-500 mr-2"></i>
                            Filter Kategori
                        </h3>
                        <div class="space-y-2 max-h-64 overflow-y-auto custom-scrollbar pr-2">
                            <a href="{{ route('items.index', array_merge(request()->except(['category', 'page']), ['category' => 'all'])) }}"
                               class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition {{ (!$filterData['category_id'] || $filterData['category_id'] == 'all') ? 'bg-blue-50 text-blue-600' : '' }}">
                                <span class="font-medium">Semua Kategori</span>
                                <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                    {{ $stats['total_items'] ?? 0 }}
                                </span>
                            </a>
                            
                            @foreach($categories as $category)
                                @php
                                    $itemCount = $category->items()->where('stock', '>', 0)->count();
                                @endphp
                                @if($itemCount > 0)
                                    <a href="{{ route('items.index', array_merge(request()->except(['category', 'page']), ['category' => $category->id])) }}"
                                       class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition {{ $filterData['category_id'] == $category->id ? 'bg-blue-50 text-blue-600' : '' }}">
                                        <span class="font-medium">{{ $category->name }}</span>
                                        <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                            {{ $itemCount }}
                                        </span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Quick Search -->
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-search text-blue-500 mr-2"></i>
                            Cari Cepat
                        </h3>
                        <form method="GET" action="{{ route('items.index') }}" class="relative">
                            <input type="text" 
                                   name="search" 
                                   value="{{ $filterData['search'] ?? '' }}"
                                   placeholder="Cari barang..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   x-on:input.debounce.500ms="$el.form.submit()">
                            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                            @if($filterData['search'] ?? false)
                                <a href="{{ route('items.index', request()->except(['search', 'page'])) }}"
                                   class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </form>
                    </div>
                    
                    <!-- Quick Tips -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                            Tips Peminjaman
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-lg">
                                <i class="fas fa-calendar-check text-blue-500 mt-1"></i>
                                <p class="text-sm text-blue-800">Ajukan minimal 1-2 hari sebelum penggunaan</p>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-green-50 rounded-lg">
                                <i class="fas fa-clock text-green-500 mt-1"></i>
                                <p class="text-sm text-green-800">Kembalikan tepat waktu untuk hindari denda</p>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-purple-50 rounded-lg">
                                <i class="fas fa-phone-alt text-purple-500 mt-1"></i>
                                <p class="text-sm text-purple-800">Hubungi admin jika ada kendala</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Popular Items -->
                @if($popularItems->count() > 0)
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-fire text-orange-500 mr-2"></i>
                        Barang Populer
                    </h3>
                    <div class="space-y-4">
                        @foreach($popularItems as $popularItem)
                            <a href="{{ route('items.show', $popularItem) }}" 
                               class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-lg transition group">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-gradient-to-r from-blue-100 to-purple-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-box text-blue-500"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-gray-800 group-hover:text-blue-600 truncate">
                                        {{ $popularItem->name }}
                                    </h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded">
                                            {{ $popularItem->category->name }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            {{ $popularItem->stock }} stok
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="flex items-center text-orange-500">
                                        <i class="fas fa-star text-sm"></i>
                                        <span class="text-xs font-bold ml-1">
                                            {{ $popularItem->peminjamans_count ?? 0 }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                <!-- Filter Status & Results Info -->
                <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex-1">
                            <!-- Active Filters -->
                            <div class="flex flex-wrap gap-2">
                                @if($filterData['search'] ?? false)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                        <span>Cari: "{{ $filterData['search'] }}"</span>
                                        <a href="{{ route('items.index', request()->except(['search', 'page'])) }}"
                                           class="hover:text-blue-900">
                                            <i class="fas fa-times text-xs"></i>
                                        </a>
                                    </span>
                                @endif
                                
                                @if(($filterData['category_id'] ?? false) && $filterData['category_id'] != 'all')
                                    @php
                                        $category = $categories->firstWhere('id', $filterData['category_id']);
                                    @endphp
                                    @if($category)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">
                                            <span>Kategori: {{ $category->name }}</span>
                                            <a href="{{ route('items.index', request()->except(['category', 'page'])) }}"
                                               class="hover:text-purple-900">
                                                <i class="fas fa-times text-xs"></i>
                                            </a>
                                        </span>
                                    @endif
                                @endif
                                
                                @if($filterData['min_stock'] ?? false)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                                        <span>Stok ≥ {{ $filterData['min_stock'] }}</span>
                                        <a href="{{ route('items.index', request()->except(['min_stock', 'page'])) }}"
                                           class="hover:text-green-900">
                                            <i class="fas fa-times text-xs"></i>
                                        </a>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Results Count & Sort -->
                        <div class="flex items-center gap-4">
                            <div class="text-sm text-gray-600">
                                Menampilkan 
                                <span class="font-bold">{{ $items->firstItem() ?? 0 }}-{{ $items->lastItem() ?? 0 }}</span>
                                dari <span class="font-bold">{{ $items->total() }}</span> barang
                            </div>
                            <div class="hidden sm:block">
                                {{ $items->onEachSide(1)->links('vendor.pagination.custom') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Grid or Empty State -->
                @if($items->count() > 0)
                    <!-- Mobile Sort (for small screens) -->
                    <div class="sm:hidden mb-4">
                        <select onchange="window.location.href = this.value" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white">
                            <option value="{{ route('items.index', array_merge(request()->except(['sort', 'order', 'page']), ['sort' => 'name', 'order' => 'asc'])) }}"
                                    {{ ($filterData['sort'] ?? '') == 'name' && ($filterData['order'] ?? '') == 'asc' ? 'selected' : '' }}>
                                Nama A-Z
                            </option>
                            <option value="{{ route('items.index', array_merge(request()->except(['sort', 'order', 'page']), ['sort' => 'created_at', 'order' => 'desc'])) }}"
                                    {{ ($filterData['sort'] ?? '') == 'created_at' ? 'selected' : '' }}>
                                Terbaru
                            </option>
                        </select>
                    </div>

                    <!-- Items Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($items as $item)
                            @php
                                // Calculate available stock
                                $borrowedQty = $item->peminjamans
                                    ->whereIn('status', ['diajukan', 'disetujui'])
                                    ->sum('quantity');
                                $availableStock = max(0, $item->stock - $borrowedQty);
                                
                                // Stock status
                                $stockClass = $availableStock == 0 ? 'bg-red-100 text-red-800' : 
                                            ($availableStock <= 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800');
                                
                                // Popularity badge
                                $popularity = $item->peminjamans_count ?? 0;
                                $isPopular = $popularity > 5;
                            @endphp
                            
                            <div class="card-hover bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                                <!-- Item Header with Badges -->
                                <div class="relative">
                                    <!-- Category Badge -->
                                    <div class="absolute top-3 left-3 z-10">
                                        <span class="inline-block px-3 py-1 bg-white/90 backdrop-blur-sm text-blue-600 text-xs font-medium rounded-full shadow-sm">
                                            {{ $item->category->name }}
                                        </span>
                                    </div>
                                    
                                    <!-- Popular Badge -->
                                    @if($isPopular)
                                        <div class="absolute top-3 right-3 z-10">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-orange-100 text-orange-800 text-xs font-medium rounded-full shadow-sm">
                                                <i class="fas fa-fire text-xs"></i>
                                                Populer
                                            </span>
                                        </div>
                                    @endif
                                    
                                    <!-- Item Image/Icon -->
                                    <div class="h-48 bg-gradient-to-r from-blue-50 to-purple-50 flex items-center justify-center relative overflow-hidden">
                                        <!-- Background Pattern -->
                                        <div class="absolute inset-0 opacity-10">
                                            <div class="absolute top-0 left-0 w-32 h-32 bg-blue-200 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                                            <div class="absolute bottom-0 right-0 w-40 h-40 bg-purple-200 rounded-full translate-x-1/3 translate-y-1/3"></div>
                                        </div>
                                        
                                        <!-- Main Icon -->
                                        <div class="relative z-10">
                                            <i class="fas fa-box text-6xl text-blue-500 opacity-70"></i>
                                        </div>
                                    </div>
                                    
                                    <!-- Stock Status Bar -->
                                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r 
                                        {{ $availableStock == 0 ? 'from-red-400 to-red-600' : 
                                           ($availableStock <= 3 ? 'from-yellow-400 to-orange-500' : 'from-green-400 to-emerald-600') }}">
                                    </div>
                                </div>
                                
                                <!-- Item Content -->
                                <div class="p-5">
                                    <!-- Item Name & Description -->
                                    <h3 class="text-xl font-bold text-gray-800 mb-2 line-clamp-1">
                                        {{ $item->name }}
                                    </h3>
                                    <p class="text-gray-600 mb-4 line-clamp-2 text-sm">
                                        {{ Str::limit($item->description, 100) }}
                                    </p>
                                    
                                    <!-- Stock & Availability -->
                                    <div class="flex items-center justify-between mb-6">
                                        <div>
                                            <p class="text-sm text-gray-500 mb-1">Stok Total</p>
                                            <p class="text-lg font-bold text-gray-800">{{ $item->stock }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-500 mb-1">Tersedia</p>
                                            <p class="text-lg font-bold {{ $availableStock == 0 ? 'text-red-600' : 
                                                                        ($availableStock <= 3 ? 'text-yellow-600' : 'text-green-600') }}">
                                                {{ $availableStock }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="flex gap-3">
                                        <!-- Detail Button -->
                                        <a href="{{ route('items.show', $item) }}" 
                                           class="flex-1 inline-flex items-center justify-center gap-2 py-2.5 px-4 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition font-medium">
                                            <i class="fas fa-info-circle"></i>
                                            <span class="hidden sm:inline">Detail</span>
                                        </a>
                                        
                                        <!-- Borrow Button or Status -->
                                        @if($availableStock > 0)
                                            @auth
                                                <button onclick="openBorrowModal(
                                                    {{ $item->id }}, 
                                                    '{{ addslashes($item->name) }}', 
                                                    {{ $availableStock }},
                                                    '{{ $item->category->name }}'
                                                )"
                                                        class="flex-1 inline-flex items-center justify-center gap-2 py-2.5 px-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg transition font-medium shadow-sm hover:shadow-md">
                                                    <i class="fas fa-handshake"></i>
                                                    <span class="hidden sm:inline">Pinjam</span>
                                                </button>
                                            @else
                                                <a href="{{ route('login') }}?redirect={{ urlencode(route('items.show', $item)) }}"
                                                   class="flex-1 inline-flex items-center justify-center gap-2 py-2.5 px-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg transition font-medium shadow-sm hover:shadow-md">
                                                    <i class="fas fa-sign-in-alt"></i>
                                                    <span class="hidden sm:inline">Login</span>
                                                </a>
                                            @endauth
                                        @else
                                            <div class="flex-1 inline-flex items-center justify-center gap-2 py-2.5 px-4 bg-gray-300 text-gray-500 rounded-lg font-medium cursor-not-allowed">
                                                <i class="fas fa-times"></i>
                                                <span class="hidden sm:inline">Habis</span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Additional Info -->
                                    <div class="mt-4 pt-4 border-t border-gray-100">
                                        <div class="flex items-center justify-between text-xs text-gray-500">
                                            <div class="flex items-center gap-1">
                                                <i class="fas fa-calendar-alt"></i>
                                                <span>{{ $item->created_at->diffForHumans() }}</span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <i class="fas fa-history"></i>
                                                <span>{{ $popularity }}x dipinjam</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    @if($items->hasPages())
                        <div class="mt-8">
                            {{ $items->onEachSide(1)->links('vendor.pagination.custom') }}
                        </div>
                    @endif
                    
                @else
                    <!-- Empty State -->
                    <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                        <div class="w-24 h-24 mx-auto mb-6 text-gray-300">
                            <i class="fas fa-box-open text-6xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-700 mb-3">Tidak ada barang ditemukan</h3>
                        <p class="text-gray-500 mb-6 max-w-md mx-auto">
                            @if($filterData['search'] ?? false)
                                Tidak ada barang yang cocok dengan pencarian "{{ $filterData['search'] }}".
                            @elseif($filterData['category_id'] ?? false)
                                Tidak ada barang dalam kategori yang dipilih.
                            @else
                                Belum ada barang yang tersedia untuk dipinjam.
                            @endif
                        </p>

                        <div class="flex flex-wrap gap-3 justify-center">
                            <a href="{{ route('items.index') }}" 
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium">
                                <i class="fas fa-redo"></i>
                                Reset Filter
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </main>


    <!-- Borrow Modal -->
    @include('items.partials.borrow-modal')

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Alpine.js Component for Filtering
        function itemFilter() {
            return {
                isLoading: false,
                searchQuery: '{{ $filterData["search"] ?? "" }}',
                selectedCategory: '{{ $filterData["category_id"] ?? "all" }}',
                
                init() {
                    console.log('Item Filter Component Initialized');
                    
                    // Initialize tooltips if any
                    this.initTooltips();
                    
                    // Listen for AJAX search
                    this.setupSearch();
                },
                
                initTooltips() {
                    // Initialize tooltips using Tippy.js or similar
                    // For now, we'll use native title attributes
                },
                
                setupSearch() {
                    const searchInput = document.querySelector('input[name="search"]');
                    if (searchInput) {
                        searchInput.addEventListener('keyup', this.debounce(() => {
                            this.submitSearch();
                        }, 500));
                    }
                },
                
                submitSearch() {
                    const form = document.getElementById('searchForm');
                    if (form) {
                        this.isLoading = true;
                        form.submit();
                    }
                },
                
                debounce(func, wait) {
                    let timeout;
                    return function executedFunction(...args) {
                        const later = () => {
                            clearTimeout(timeout);
                            func(...args);
                        };
                        clearTimeout(timeout);
                        timeout = setTimeout(later, wait);
                    };
                },
                
                filterByCategory(categoryId) {
                    this.selectedCategory = categoryId;
                    this.updateUrl();
                },
                
                updateUrl() {
                    const params = new URLSearchParams(window.location.search);
                    
                    if (this.selectedCategory !== 'all') {
                        params.set('category', this.selectedCategory);
                    } else {
                        params.delete('category');
                    }
                    
                    if (this.searchQuery.trim()) {
                        params.set('search', this.searchQuery.trim());
                    } else {
                        params.delete('search');
                    }
                    
                    params.delete('page'); // Reset to first page
                    
                    window.location.href = `${window.location.pathname}?${params.toString()}`;
                },
                
                clearFilters() {
                    window.location.href = '{{ route("items.index") }}';
                }
            };
        }

        // Global Functions
        function toggleAdvancedFilters() {
            const filters = document.getElementById('advancedFilters');
            if (filters) {
                filters.classList.toggle('hidden');
                filters.classList.toggle('fade-in');
            }
        }

        function resetAdvancedFilters() {
            const form = document.getElementById('advancedFilterForm');
            if (form) {
                form.reset();
                form.submit();
            }
        }

        function showLoading() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                overlay.classList.remove('hidden');
            }
        }

        function hideLoading() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                overlay.classList.add('hidden');
            }
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Hide loading overlay
            hideLoading();
            
            // Initialize datepickers in modal
            if (typeof flatpickr !== 'undefined') {
                flatpickr('.datepicker', {
                    dateFormat: 'Y-m-d',
                    locale: 'id',
                    minDate: 'today',
                    disable: []
                });
            }
            
            // Handle AJAX search
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                let searchTimeout;
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        if (this.value.length >= 2 || this.value.length === 0) {
                            showLoading();
                            this.form.submit();
                        }
                    }, 800);
                });
            }
            
            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
            
            // Handle modal triggers
            window.openBorrowModal = function(itemId, itemName, maxStock, categoryName) {
                // This function will be implemented in the modal partial
                console.log('Open borrow modal for:', itemId, itemName, maxStock, categoryName);
                
                // Show the modal (implementation depends on modal structure)
                const modal = document.getElementById('borrowModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    
                    // Set modal content
                    document.getElementById('modalItemId').value = itemId;
                    document.getElementById('modalItemName').textContent = itemName;
                    document.getElementById('modalCategoryName').textContent = categoryName;
                    document.getElementById('modalMaxStock').textContent = maxStock;
                    document.getElementById('quantity').max = maxStock;
                    
                    // Initialize date pickers
                    const today = new Date().toISOString().split('T')[0];
                    const tomorrow = new Date(Date.now() + 86400000).toISOString().split('T')[0];
                    
                    const pinjamInput = document.getElementById('tanggal_pinjam');
                    const kembaliInput = document.getElementById('tanggal_kembali');
                    
                    if (pinjamInput) {
                        pinjamInput.min = today;
                        pinjamInput.value = today;
                    }
                    
                    if (kembaliInput) {
                        kembaliInput.min = tomorrow;
                        kembaliInput.value = tomorrow;
                    }
                }
            };
            
            window.closeBorrowModal = function() {
                const modal = document.getElementById('borrowModal');
                if (modal) {
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }
            };
        });

        // Handle form submissions with loading state
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                showLoading();
            });
        });

        // Handle page transitions
        document.addEventListener('page:fetch', showLoading);
        document.addEventListener('page:change', hideLoading);
        document.addEventListener('page:restore', hideLoading);

        // Error handling for AJAX requests
        window.addEventListener('unhandledrejection', function(event) {
            console.error('Unhandled rejection:', event.reason);
            hideLoading();
            alert('Terjadi kesalahan. Silakan refresh halaman.');
        });

        // Online/Offline detection
        window.addEventListener('online', function() {
            console.log('Connection restored');
            // You could trigger a refresh or show a notification
        });

        window.addEventListener('offline', function() {
            console.log('Connection lost');
            alert('Koneksi internet terputus. Beberapa fitur mungkin tidak berfungsi.');
        });
    </script>
    
    <!-- Service Worker for PWA (Optional) -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/service-worker.js').then(
                    function(registration) {
                        console.log('ServiceWorker registration successful');
                    },
                    function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    }
                );
            });
        }
    </script>
</body>
</html>
=======
<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <style>
            /* Custom Scrollbar */
            .custom-scrollbar::-webkit-scrollbar { width: 6px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: #f3f4f6; border-radius: 8px; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 8px; }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
            
            /* Line Clamp untuk deskripsi */
            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
        </style>
    @endpush

    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <i class="fas fa-boxes text-indigo-600"></i>
                {{ __('Katalog Inventaris') }}
            </h2>
            
            @if(auth()->check() && method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin())
                <a href="{{ route('admin.items.create') }}" 
                   class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-full shadow-lg hover:shadow-indigo-500/30 transition-all duration-300 transform hover:-translate-y-0.5">
                    <i class="fas fa-plus mr-2"></i> Tambah Barang
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen" x-data="itemFilter()" x-init="init()">
        
        <div x-show="isLoading" 
             x-transition.opacity
             class="fixed inset-0 bg-white/80 backdrop-blur-sm z-50 flex items-center justify-center">
            <div class="flex flex-col items-center">
                <div class="w-12 h-12 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
                <p class="mt-3 text-indigo-600 font-medium animate-pulse">Memuat data...</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition duration-300 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 h-full w-1 bg-indigo-500 rounded-r-2xl"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Aset</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_items'] ?? 0 }}</h3>
                        </div>
                        <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition duration-300">
                            <i class="fas fa-box"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition duration-300 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 h-full w-1 bg-emerald-500 rounded-r-2xl"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tersedia</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['available_items'] ?? 0 }}</h3>
                        </div>
                        <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition duration-300">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition duration-300 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 h-full w-1 bg-purple-500 rounded-r-2xl"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Kategori</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_categories'] ?? 0 }}</h3>
                        </div>
                        <div class="p-3 bg-purple-50 rounded-xl text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition duration-300">
                            <i class="fas fa-tags"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition duration-300 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 h-full w-1 bg-orange-500 rounded-r-2xl"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Sedang Dipinjam</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_borrowings'] ?? 0 }}</h3>
                        </div>
                        <div class="p-3 bg-orange-50 rounded-xl text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition duration-300">
                            <i class="fas fa-handshake"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                
                <div class="lg:col-span-1 space-y-6">
                    <div class="sticky top-24 space-y-6">
                        
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-search text-indigo-500 mr-2"></i> Pencarian
                            </h3>
                            <form method="GET" action="{{ route('items.index') }}" class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400 group-focus-within:text-indigo-500 transition"></i>
                                </div>
                                <input type="text" 
                                       name="search" 
                                       value="{{ $filterData['search'] ?? '' }}" 
                                       placeholder="Cari nama barang..."
                                       class="w-full pl-10 pr-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 rounded-xl text-sm transition-all duration-300"
                                       x-on:input.debounce.500ms="$el.form.submit()">
                            </form>
                        </div>

                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h3 class="font-bold text-gray-800 mb-4 flex items-center justify-between">
                                <span><i class="fas fa-filter text-indigo-500 mr-2"></i> Kategori</span>
                                @if(request('category') && request('category') != 'all')
                                    <a href="{{ route('items.index') }}" class="text-xs text-red-500 hover:text-red-700 font-medium">Reset</a>
                                @endif
                            </h3>
                            
                            <div class="space-y-2 max-h-[300px] overflow-y-auto custom-scrollbar pr-1">
                                <a href="{{ route('items.index', array_merge(request()->except(['category', 'page']), ['category' => 'all'])) }}"
                                   class="flex items-center justify-between p-3 rounded-xl transition-all duration-200 group {{ (!$filterData['category_id'] || $filterData['category_id'] == 'all') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'hover:bg-gray-50 text-gray-600' }}">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full {{ (!$filterData['category_id'] || $filterData['category_id'] == 'all') ? 'bg-indigo-500' : 'bg-gray-300 group-hover:bg-indigo-400' }}"></div>
                                        <span>Semua</span>
                                    </div>
                                </a>

                                @foreach($categories as $category)
                                    @php $isActive = $filterData['category_id'] == $category->id; @endphp
                                    <a href="{{ route('items.index', array_merge(request()->except(['category', 'page']), ['category' => $category->id])) }}"
                                       class="flex items-center justify-between p-3 rounded-xl transition-all duration-200 group {{ $isActive ? 'bg-indigo-50 text-indigo-700 font-semibold shadow-sm' : 'hover:bg-gray-50 text-gray-600' }}">
                                        <div class="flex items-center gap-3">
                                            <div class="w-2 h-2 rounded-full {{ $isActive ? 'bg-indigo-500' : 'bg-gray-300 group-hover:bg-indigo-400' }}"></div>
                                            <span>{{ $category->name }}</span>
                                        </div>
                                        @if($category->items_count > 0)
                                            <span class="text-xs py-0.5 px-2 rounded-md {{ $isActive ? 'bg-indigo-200 text-indigo-800' : 'bg-gray-100 text-gray-500' }}">
                                                {{ $category->items_count }}
                                            </span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <button onclick="toggleAdvancedFilters()" class="w-full py-3 px-4 bg-white border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 hover:text-indigo-600 font-medium transition-all shadow-sm flex items-center justify-center gap-2">
                            <i class="fas fa-sliders-h"></i> Filter Lanjutan
                        </button>

                        <div id="advancedFilters" class="hidden" x-transition>
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-4">
                                <form method="GET" action="{{ route('items.index') }}" class="space-y-4">
                                    @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                                    @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif

                                    <div>
                                        <label class="text-xs font-bold text-gray-500 uppercase mb-2 block">Urutan</label>
                                        <select name="sort" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="name">Nama (A-Z)</option>
                                            <option value="created_at">Terbaru</option>
                                            <option value="popular">Terpopuler</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="text-xs font-bold text-gray-500 uppercase mb-2 block">Stok Minimum</label>
                                        <input type="number" name="min_stock" placeholder="0" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                                        Terapkan
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="lg:col-span-3">
                    
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                        <p class="text-gray-600 text-sm">
                            Menampilkan <span class="font-bold text-gray-900">{{ $items->firstItem() ?? 0 }}-{{ $items->lastItem() ?? 0 }}</span> 
                            dari <span class="font-bold text-gray-900">{{ $items->total() }}</span> barang
                        </p>
                        
                        <div class="sm:hidden w-full">
                            <select onchange="window.location.href = this.value" class="w-full rounded-lg border-gray-300 text-sm">
                                <option value="{{ route('items.index', array_merge(request()->all(), ['sort' => 'name'])) }}">Nama A-Z</option>
                                <option value="{{ route('items.index', array_merge(request()->all(), ['sort' => 'created_at'])) }}">Terbaru</option>
                            </select>
                        </div>
                    </div>

                    @if($items->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($items as $item)
                                @php
                                    $borrowedQty = $item->peminjamans ? $item->peminjamans->whereIn('status', ['diajukan', 'disetujui'])->sum('quantity') : 0;
                                    $availableStock = max(0, $item->stock - $borrowedQty);
                                    $isPopular = ($item->peminjamans_count ?? 0) > 5;
                                @endphp

                                <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col h-full relative overflow-hidden">
                                    
                                    <div class="relative aspect-[4/3] bg-gray-50 flex items-center justify-center overflow-hidden group-hover:bg-indigo-50/30 transition">
                                        <div class="absolute top-3 left-3 z-10 flex flex-wrap gap-2">
                                            <span class="px-2.5 py-1 bg-white/90 backdrop-blur-md text-xs font-semibold text-indigo-600 rounded-lg shadow-sm border border-white/50">
                                                {{ $item->category->name ?? 'Umum' }}
                                            </span>
                                        </div>

                                        @if($isPopular)
                                            <div class="absolute top-3 right-3 z-10">
                                                <span class="px-2.5 py-1 bg-orange-500/90 backdrop-blur-md text-xs font-bold text-white rounded-lg shadow-sm flex items-center gap-1">
                                                    <i class="fas fa-fire"></i> Hot
                                                </span>
                                            </div>
                                        @endif

                                        @if($item->photo)
                                            <img src="{{ asset('storage/' . $item->photo) }}" 
                                                 alt="{{ $item->name }}" 
                                                 class="w-full h-full object-contain p-6 transform group-hover:scale-110 transition-transform duration-500">
                                        @else
                                            <i class="fas fa-box text-5xl text-gray-300 group-hover:text-indigo-300 transition-colors duration-300"></i>
                                        @endif

                                        <div class="absolute bottom-0 left-0 right-0 h-1.5 bg-gray-100">
                                            @php $percent = ($item->stock > 0) ? ($availableStock / $item->stock) * 100 : 0; @endphp
                                            <div class="h-full transition-all duration-500 {{ $availableStock == 0 ? 'bg-red-500' : ($availableStock <= 3 ? 'bg-yellow-500' : 'bg-emerald-500') }}" 
                                                 style="width: {{ $percent }}%"></div>
                                        </div>
                                    </div>

                                    <div class="p-5 flex flex-col flex-1">
                                        <div class="mb-3">
                                            <h3 class="font-bold text-gray-900 text-lg leading-tight mb-1 group-hover:text-indigo-600 transition">
                                                {{ $item->name }}
                                            </h3>
                                            <p class="text-sm text-gray-500 line-clamp-2">
                                                {{ $item->description ?? 'Tidak ada deskripsi.' }}
                                            </p>
                                        </div>

                                        <div class="mt-auto pt-4 border-t border-gray-50">
                                            <div class="flex justify-between items-center mb-4">
                                                <div class="text-xs font-medium text-gray-400 uppercase tracking-wide">Ketersediaan</div>
                                                <div class="text-sm font-bold {{ $availableStock > 0 ? 'text-emerald-600' : 'text-red-500' }}">
                                                    {{ $availableStock }} <span class="text-gray-400 font-normal">/ {{ $item->stock }}</span>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 gap-3">
                                                <a href="{{ route('items.show', $item) }}" 
                                                   class="inline-flex justify-center items-center py-2.5 px-4 rounded-xl bg-gray-50 text-gray-700 hover:bg-gray-100 font-medium text-sm transition duration-200">
                                                    Detail
                                                </a>
                                                
                                                @if($availableStock > 0)
                                                    <button onclick="openBorrowModal({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $availableStock }}, '{{ $item->category->name ?? '' }}')"
                                                            class="inline-flex justify-center items-center py-2.5 px-4 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 font-medium text-sm shadow-md shadow-indigo-200 hover:shadow-indigo-300 transition duration-200">
                                                        Pinjam
                                                    </button>
                                                @else
                                                    <button disabled class="inline-flex justify-center items-center py-2.5 px-4 rounded-xl bg-gray-100 text-gray-400 font-medium text-sm cursor-not-allowed">
                                                        Habis
                                                    </button>
                                                @endif
                                            </div>
                                            
                                            @if(auth()->check() && method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin())
                                                <div class="flex justify-end gap-2 mt-3 pt-2 border-t border-gray-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                                    <a href="{{ route('admin.items.edit', $item) }}" class="text-yellow-600 hover:text-yellow-700 text-xs font-medium">Edit</a>
                                                    <span class="text-gray-300">|</span>
                                                    <form action="{{ route('admin.items.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus barang ini?');" class="inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-700 text-xs font-medium">Hapus</button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-10">
                            {{ $items->onEachSide(1)->links() }}
                        </div>

                    @else
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-12 text-center">
                            <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-search text-3xl text-indigo-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Barang tidak ditemukan</h3>
                            <p class="text-gray-500 mb-8 max-w-md mx-auto">
                                Maaf, kami tidak dapat menemukan barang yang sesuai dengan kriteria pencarian Anda. Coba kata kunci lain atau reset filter.
                            </p>
                            <a href="{{ route('items.index') }}" 
                               class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                                <i class="fas fa-redo-alt mr-2"></i> Reset Filter
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('items.partials.borrow-modal')

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
        <script>
            function itemFilter() {
                return {
                    isLoading: false,
                    init() {
                        // Optional: Add loading simulation on pagination click
                        document.querySelectorAll('.pagination a').forEach(link => {
                            link.addEventListener('click', () => { this.isLoading = true; });
                        });
                    }
                }
            }

            function toggleAdvancedFilters() {
                const el = document.getElementById('advancedFilters');
                el.classList.toggle('hidden');
            }
        </script>
    @endpush
</x-app-layout>
>>>>>>> d55b9af1f343e0e3324d653f7222d76df8c70cd2
