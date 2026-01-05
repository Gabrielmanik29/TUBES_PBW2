<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Barang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <nav class="mb-6 text-sm">
                        <ol class="flex items-center space-x-2 text-gray-600">
                            <li><a href="{{ route('items.index') }}" class="hover:text-purple-600">Beranda</a></li>
                            <li class="text-gray-400">/</li>
                            <li><a href="{{ route('items.index', ['category' => $item->category_id]) }}"
                                    class="hover:text-purple-600">{{ $item->category->name ?? 'Kategori' }}</a></li>
                            <li class="text-gray-400">/</li>
                            <li class="text-gray-800 font-medium">{{ $item->name }}</li>
                        </ol>
                    </nav>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="bg-gray-100 rounded-xl overflow-hidden shadow-lg">
                            <div class="aspect-square flex items-center justify-center bg-gray-50">
                                @if($item->photo)
                                    <img src="{{ asset('storage/' . $item->photo) }}" alt="{{ $item->name }}"
                                        class="max-w-full max-h-full object-contain"
                                        onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'flex flex-col items-center justify-center text-gray-400\'><i class=\'fas fa-box text-8xl mb-4\'></i><p>Foto tidak tersedia</p></div>';">
                                @else
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <i class="fas fa-box text-8xl mb-4"></i>
                                        <p class="text-lg">Foto tidak tersedia</p>
                                    </div>
                                @endif
                            </div>

                            <div class="p-4 bg-white border-t">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">Stok Tersedia:</span>
                                    <span
                                        class="text-2xl font-bold {{ $stokTersedia > 5 ? 'text-green-600' : ($stokTersedia > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $stokTersedia }} / {{ $item->stock }}
                                    </span>
                                </div>
                                <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $stokTersedia > 5 ? 'bg-green-500' : ($stokTersedia > 0 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                        style="width: {{ ($stokTersedia / max($item->stock, 1)) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="mb-4">
                                <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ $item->category->name ?? 'Uncategorized' }}
                                </span>
                            </div>

                            <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $item->name }}</h1>

                            <div class="prose max-w-none text-gray-600 mb-6">
                                <h3 class="text-lg font-semibold mb-2">Deskripsi</h3>
                                <p>{{ $item->description ?? 'Tidak ada deskripsi tersedia.' }}</p>
                            </div>

                            <div class="grid grid-cols-3 gap-4 mb-6">
                                <div class="bg-blue-50 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-blue-600">{{ $itemStats['total_borrowings'] }}
                                    </div>
                                    <div class="text-xs text-gray-600">Total Pinjam</div>
                                </div>
                                <div class="bg-green-50 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-green-600">
                                        {{ $itemStats['returned_borrowings'] }}</div>
                                    <div class="text-xs text-gray-600">Dikembalikan</div>
                                </div>
                                <div class="bg-yellow-50 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-yellow-600">
                                        {{ $itemStats['active_borrowings'] }}</div>
                                    <div class="text-xs text-gray-600">Sedang Dipinjam</div>
                                </div>
                            </div>

                            @auth
                                @if($stokTersedia > 0)
                                    <button onclick="openBorrowModal({{ $item->id }})"
                                        class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-4 px-6 rounded-xl transition duration-300 flex items-center justify-center gap-2">
                                        <i class="fas fa-shopping-cart"></i>
                                        Ajukan Peminjaman
                                    </button>
                                @else
                                    <div
                                        class="w-full bg-gray-300 text-gray-600 font-bold py-4 px-6 rounded-xl text-center cursor-not-allowed">
                                        <i class="fas fa-times-circle"></i>
                                        Stok Habis
                                    </div>
                                @endif

                                @if($userBorrowingHistory)
                                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                        <p class="text-sm text-yellow-800">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Anda pernah meminjam barang ini.
                                            Status: <strong>{{ $userBorrowingHistory->status }}</strong>
                                        </p>
                                    </div>
                                @endif
                            @else
                                <a href="{{ route('login') }}"
                                    class="block w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-4 px-6 rounded-xl text-center transition duration-300">
                                    <i class="fas fa-sign-in-alt mr-2"></i>
                                    Login untuk Meminjam
                                </a>
                            @endauth
                        </div>
                    </div>

                    @if($relatedItems->count() > 0)
                        <div class="mt-12">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6">Barang Terkait</h2>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                @foreach($relatedItems as $related)
                                    <a href="{{ route('items.show', $related) }}" class="group">
                                        <div
                                            class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                                            <div class="aspect-square bg-gray-100 flex items-center justify-center">
                                                @if($related->photo)
                                                    <img src="{{ asset('storage/' . $related->photo) }}" alt="{{ $related->name }}"
                                                        class="max-w-full max-h-full object-contain group-hover:scale-105 transition duration-300"
                                                        onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fas fa-box text-4xl text-gray-400\'></i>';">
                                                @else
                                                    <i class="fas fa-box text-4xl text-gray-400"></i>
                                                @endif
                                            </div>
                                            <div class="p-4">
                                                <h3
                                                    class="font-semibold text-gray-800 truncate group-hover:text-purple-600 transition">
                                                    {{ $related->name }}
                                                </h3>
                                                <p class="text-sm text-gray-500">{{ $related->category->name ?? '' }}</p>
                                                <p class="text-sm font-medium text-green-600 mt-1">{{ $related->stock }}
                                                    tersedia</p>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('items.partials.borrow-modal')

    @push('scripts')
        <script>
            function openBorrowModal(itemId) {
                // Open the modal with all required parameters
                @auth
                            const availableStock = {{ $stokTersedia }};
                    const itemName = '{{ addslashes($item->name) }}';
                    const categoryName = '{{ $item->category->name ?? '' }}';

                    if (typeof openBorrowModalEnhanced === 'function') {
                        openBorrowModalEnhanced(itemId, itemName, availableStock, categoryName);
                    } else if (typeof openBorrowModal === 'function') {
                        openBorrowModal(itemId, itemName, availableStock, categoryName);
                    } else {
                        // Fallback - directly call the function from borrow-modal
                        const modal = document.getElementById('borrowModal');
                        const modalContent = document.getElementById('borrowModalContent');

                        if (modal && modalContent) {
                            document.getElementById('modalItemId').value = itemId;
                            document.getElementById('modalItemName').textContent = itemName;
                            document.getElementById('modalCategoryName').textContent = categoryName;
                            document.getElementById('modalMaxStock').textContent = availableStock;
                            document.getElementById('quantityMax').textContent = availableStock;
                            document.getElementById('quantity').max = availableStock;
                            document.getElementById('quantity').value = 1;

                            const today = new Date().toISOString().split('T')[0];
                            const tomorrow = new Date(Date.now() + 86400000).toISOString().split('T')[0];
                            document.getElementById('tanggal_pinjam').value = today;
                            document.getElementById('tanggal_kembali').value = tomorrow;

                            modal.classList.remove('hidden');
                            setTimeout(() => {
                                modalContent.classList.remove('scale-95', 'opacity-0');
                                modalContent.classList.add('scale-100', 'opacity-100');
                            }, 10);
                            document.body.style.overflow = 'hidden';
                        }
                    }
                @else
                    window.location.href = '/login';
                @endauth
                }
        </script>
    @endpush
</x-app-layout>