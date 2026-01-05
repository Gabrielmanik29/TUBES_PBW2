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