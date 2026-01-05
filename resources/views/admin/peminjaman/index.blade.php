<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kelola Peminjaman') }}
            </h2>
            <div class="flex items-center gap-2">
                {{-- Tombol Export (Opsional) --}}
                <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg text-sm shadow transition">
                    <i class="fas fa-file-excel mr-2"></i> Export Excel
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- FLASH MESSAGE --}}
            @if(session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
                    <p class="font-bold">Berhasil!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                    <p class="font-bold">Gagal!</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- FILTER --}}
                    <div class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <form method="GET" action="{{ route('admin.peminjaman') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            {{-- Search --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Cari</label>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Nama barang / peminjam..."
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            </div>

                            {{-- Status --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                                <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="">Semua Status</option>
                                    <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                    <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                                </select>
                            </div>

                            {{-- Tanggal --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            </div>

                            {{-- Buttons --}}
                            <div class="flex items-end gap-2">
                                <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm transition">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="{{ route('admin.peminjaman') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm transition">
                                    <i class="fas fa-redo"></i>
                                </a>
                            </div>
                        </form>
                    </div>

                    {{-- STATS --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                            <div class="text-xs text-blue-600 font-bold uppercase">Total</div>
                            <div class="text-2xl font-bold text-blue-900">{{ $peminjamans->total() }}</div>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100">
                            <div class="text-xs text-yellow-600 font-bold uppercase">Menunggu</div>
                            <div class="text-2xl font-bold text-yellow-900">{{ \App\Models\Peminjaman::where('status', 'diajukan')->count() }}</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-xl border border-green-100">
                            <div class="text-xs text-green-600 font-bold uppercase">Disetujui</div>
                            <div class="text-2xl font-bold text-green-900">{{ \App\Models\Peminjaman::where('status', 'disetujui')->count() }}</div>
                        </div>
                        <div class="bg-red-50 p-4 rounded-xl border border-red-100">
                            <div class="text-xs text-red-600 font-bold uppercase">Ditolak</div>
                            <div class="text-2xl font-bold text-red-900">{{ \App\Models\Peminjaman::where('status', 'ditolak')->count() }}</div>
                        </div>
                    </div>

                    {{-- TABLE --}}
                    <div class="overflow-x-auto border border-gray-200 rounded-xl">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Peminjam</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Barang</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Jadwal</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($peminjamans as $peminjaman)
                                    @php
                                        $statusColors = [
                                            'diajukan' => 'bg-yellow-100 text-yellow-800',
                                            'disetujui' => 'bg-green-100 text-green-800',
                                            'ditolak' => 'bg-red-100 text-red-800',
                                            'dikembalikan' => 'bg-blue-100 text-blue-800',
                                        ];
                                        $badge = $statusColors[$peminjaman->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp

                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            #{{ $peminjaman->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ optional($peminjaman->user)->name ?? '-' }}</div>
                                            <div class="text-xs text-gray-500">{{ optional($peminjaman->user)->email ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ optional($peminjaman->item)->name ?? '-' }}</div>
                                            <div class="text-xs text-gray-500">Qty: {{ $peminjaman->quantity }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div><i class="fas fa-arrow-up text-green-500 mr-1"></i> {{ $peminjaman->tanggal_pinjam?->format('d M') }}</div>
                                            <div><i class="fas fa-arrow-down text-red-500 mr-1"></i> {{ $peminjaman->tanggal_kembali?->format('d M') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $badge }}">
                                                {{ ucfirst($peminjaman->status) }}
                                            </span>
                                            @if($peminjaman->denda > 0)
                                                <div class="text-xs text-red-600 font-bold mt-1">
                                                    Denda: Rp {{ number_format($peminjaman->denda, 0, ',', '.') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @if($peminjaman->status === 'diajukan')
                                                <div class="flex gap-2">
                                                    <form method="POST" action="{{ route('admin.peminjaman.approve', $peminjaman) }}">
                                                        @csrf
                                                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs transition" onclick="return confirm('Setujui?')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <button type="button" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs transition" onclick="showRejectModal({{ $peminjaman->id }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            @elseif($peminjaman->status === 'disetujui')
                                                <form action="{{ route('admin.peminjaman.return', $peminjaman) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" onclick="return confirm('Konfirmasi pengembalian?')" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs transition">
                                                        Kembalikan
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-400 text-xs">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                            Tidak ada data peminjaman.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- PAGINATION --}}
                    <div class="mt-4">
                        {{ $peminjamans->withQueryString()->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- MODAL REJECT --}}
    <div id="rejectModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform transition-all scale-100">
            <div class="bg-red-50 px-6 py-4 border-b border-red-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-red-800">Tolak Peminjaman</h3>
                <button onclick="closeRejectModal()" class="text-red-400 hover:text-red-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="rejectForm" method="POST" class="p-6">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                    <textarea name="rejection_reason" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                        rows="4" placeholder="Contoh: Stok barang rusak atau tidak tersedia..."></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-bold shadow transition">
                        Tolak Peminjaman
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function showRejectModal(id) {
            document.getElementById('rejectForm').action = `/admin/peminjaman/${id}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            const modal = document.getElementById('rejectModal');
            if (e.target === modal) closeRejectModal();
        });
    </script>
    @endpush
</x-app-layout>