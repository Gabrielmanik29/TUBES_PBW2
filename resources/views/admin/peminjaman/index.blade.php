<x-app-layout>
    {{-- ========================================================== --}}
    {{-- KELOLA PEMINJAMAN - Unified View --}}
    {{-- Menampilkan semua data dengan filter & dynamic actions --}}
    {{-- ========================================================== --}}

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    {{ __('Kelola Peminjaman') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Kelola semua permintaan peminjaman barang</p>
            </div>
            <a href="{{ route('admin.laporan.index') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                <i class="fas fa-file-alt mr-2"></i>
                Laporan
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-3 text-green-500"></i>
                        <p class="font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
                        <p class="font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            {{-- Stats Cards --}}
            @if(isset($stats))
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase">Total</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
                            </div>
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clipboard-list text-gray-500"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-yellow-600 uppercase">Menunggu</p>
                                <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['diajukan'] }}</p>
                            </div>
                            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-yellow-500"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-green-600 uppercase">Dipinjam</p>
                                <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['disetujui'] }}</p>
                            </div>
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-handshake text-green-500"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-blue-600 uppercase">Dikembalikan</p>
                                <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['dikembalikan'] }}</p>
                            </div>
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-undo text-blue-500"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Main Content Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                {{-- Filter Section --}}
                <div class="p-4 border-b border-gray-100 bg-gray-50">
                    <form method="GET" action="{{ route('admin.peminjamans.index') }}"
                        class="flex flex-col sm:flex-row gap-4">
                        {{-- Search --}}
                        <div class="flex-1">
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Cari peminjam atau barang..."
                                    class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            </div>
                        </div>

                        {{-- Status Filter --}}
                        <div class="w-full sm:w-48">
                            <select name="status" onchange="this.form.submit()"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white">
                                <option value="">Semua Status</option>
                                <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>
                                    Menunggu Approval
                                </option>
                                <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>
                                    Sedang Dipinjam
                                </option>
                                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>
                                    Ditolak
                                </option>
                                <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>
                                    Dikembalikan
                                </option>
                            </select>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex gap-2">
                            <button type="submit"
                                class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                                <i class="fas fa-search mr-1"></i> Filter
                            </button>
                            <a href="{{ route('admin.peminjamans.index') }}"
                                class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition">
                                <i class="fas fa-redo-alt"></i>
                            </a>
                        </div>
                    </form>
                </div>

                {{-- Table Section --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    ID</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Peminjam</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Barang</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Tgl Pinjam</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Tgl Kembali</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($peminjamans as $peminjaman)
                                @php
                                    $statusConfig = [
                                        'diajukan' => [
                                            'badge' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                                            'label' => 'Menunggu',
                                            'icon' => 'fa-clock'
                                        ],
                                        'disetujui' => [
                                            'badge' => 'bg-green-100 text-green-800 border border-green-200',
                                            'label' => 'Dipinjam',
                                            'icon' => 'fa-check'
                                        ],
                                        'ditolak' => [
                                            'badge' => 'bg-red-100 text-red-800 border border-red-200',
                                            'label' => 'Ditolak',
                                            'icon' => 'fa-times'
                                        ],
                                        'dikembalikan' => [
                                            'badge' => 'bg-blue-100 text-blue-800 border border-blue-200',
                                            'label' => 'Dikembalikan',
                                            'icon' => 'fa-undo'
                                        ],
                                    ];
                                    $status = $statusConfig[$peminjaman->status] ?? [
                                        'badge' => 'bg-gray-100 text-gray-800',
                                        'label' => ucfirst($peminjaman->status),
                                        'icon' => 'fa-circle'
                                    ];
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    {{-- ID --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-mono text-gray-500">#{{ $peminjaman->id }}</span>
                                    </td>

                                    {{-- Peminjam --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div
                                                class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center mr-3">
                                                <span
                                                    class="text-white text-xs font-bold">{{ strtoupper(substr($peminjaman->user->name ?? 'U', 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $peminjaman->user->name ?? '-' }}</p>
                                                <p class="text-xs text-gray-500">{{ $peminjaman->user->email ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Barang --}}
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-medium text-gray-900">{{ $peminjaman->item->name ?? '-' }}
                                        </p>
                                        <p class="text-xs text-gray-500">Qty: {{ $peminjaman->quantity }}</p>
                                        @if($peminjaman->item && $peminjaman->item->category)
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 mt-1">
                                                {{ $peminjaman->item->category->name }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Tanggal Pinjam --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-arrow-up text-green-500 mr-2 text-xs"></i>
                                            {{ $peminjaman->tanggal_pinjam ? \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') : '-' }}
                                        </div>
                                    </td>

                                    {{-- Tanggal Kembali --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-arrow-down text-red-500 mr-2 text-xs"></i>
                                            {{ $peminjaman->tanggal_kembali ? \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->format('d/m/Y') : '-' }}
                                        </div>
                                        @if($peminjaman->tanggal_pengembalian_aktual)
                                            <p class="text-xs text-blue-600 mt-1">
                                                Dikembalikan:
                                                {{ \Carbon\Carbon::parse($peminjaman->tanggal_pengembalian_aktual)->format('d/m/Y') }}
                                            </p>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status['badge'] }}">
                                            <i class="fas {{ $status['icon'] }} mr-1"></i>
                                            {{ $status['label'] }}
                                        </span>
                                        @if($peminjaman->denda > 0)
                                            <div class="mt-1">
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-700">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                                    Rp {{ number_format($peminjaman->denda, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- AKSI - Dynamic based on status --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex items-center gap-2">
                                            {{-- STATUS: DIAJUKAN (Pending) --}}
                                            @if($peminjaman->status === 'diajukan')
                                                {{-- Setuju Button --}}
                                                <form action="{{ route('admin.peminjaman.approve', $peminjaman) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" onclick="return confirm('Setujui peminjaman ini?')"
                                                        class="inline-flex items-center px-2.5 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded transition shadow-sm"
                                                        title="Setujui">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>

                                                {{-- Tolak Button --}}
                                                <button type="button" onclick="showRejectModal({{ $peminjaman->id }})"
                                                    class="inline-flex items-center px-2.5 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded transition shadow-sm"
                                                    title="Tolak">
                                                    <i class="fas fa-times"></i>
                                                </button>

                                                {{-- STATUS: DISETUJUI (Sedang Dipinjam) --}}
                                            @elseif($peminjaman->status === 'disetujui')
                                                <button type="button"
                                                    onclick="showReturnModal({{ $peminjaman->id }}, '{{ $peminjaman->tanggal_kembali }}')"
                                                    class="inline-flex items-center px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded transition shadow-sm">
                                                    <i class="fas fa-undo mr-1"></i>
                                                    Kembalikan
                                                </button>

                                                {{-- STATUS: SELESAI (dikembalikan/ditolak) --}}
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-500 text-xs font-medium rounded border border-gray-200">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Selesai
                                                </span>
                                            @endif

                                            {{-- Denda Actions (if applicable) --}}
                                            @if($peminjaman->denda > 0 && !$peminjaman->denda_dibayar && $peminjaman->status === 'dikembalikan')
                                                <div class="flex gap-1">
                                                    {{-- Pay Denda Button --}}
                                                    <a href="{{ route('denda.detail', $peminjaman) }}"
                                                        target="_blank"
                                                        class="inline-flex items-center px-2.5 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded transition shadow-sm"
                                                        title="Bayar Denda">
                                                        <i class="fas fa-credit-card"></i>
                                                    </a>
                                                    {{-- Confirm Denda Paid Button --}}
                                                    <button type="button" onclick="confirmDendaPaid({{ $peminjaman->id }})"
                                                        class="inline-flex items-center px-2.5 py-1.5 bg-orange-500 hover:bg-orange-600 text-white text-xs font-medium rounded transition shadow-sm"
                                                        title="Konfirmasi Pembayaran Denda">
                                                        <i class="fas fa-money-bill-wave"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                                            <p class="text-gray-500 text-lg font-medium">Tidak ada data peminjaman</p>
                                            <p class="text-gray-400 text-sm mt-1">Coba ubah filter atau tunggu pengajuan
                                                baru</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($peminjamans->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        {{ $peminjamans->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ========================================================== --}}
    {{-- MODAL: REJECT PEMINJAMAN --}}
    {{-- ========================================================== --}}
    <div id="rejectModal"
        class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform transition-all">
            <div class="bg-red-50 px-6 py-4 border-b border-red-100 flex items-center">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-times text-red-500"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-red-800">Tolak Peminjaman</h3>
                    <p class="text-sm text-red-600">Berikan alasan penolakan</p>
                </div>
            </div>

            <form id="rejectForm" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="rejection_reason" id="rejection_reason">

                <div class="mb-4">
                    <label for="rejection_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-comment-alt mr-1 text-gray-400"></i>
                        Alasan Penolakan
                    </label>
                    <textarea id="rejection_notes" rows="4" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
                        placeholder="Contoh: Stok barang tidak tersedia atau barang sedang dalam perbaikan..."></textarea>
                    <p class="text-xs text-gray-500 mt-2">Alasan ini akan ditampilkan kepada peminjam.</p>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeRejectModal()"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium shadow-sm transition">
                        <i class="fas fa-ban mr-1"></i> Tolak Peminjaman
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================== --}}
    {{-- MODAL: PENGEMBALIAN BARANG --}}
    {{-- ========================================================== --}}
    <div id="returnModal"
        class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden transform transition-all">
            <div class="bg-blue-50 px-6 py-4 border-b border-blue-100 flex items-center">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-undo text-blue-500"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-blue-800">Proses Pengembalian</h3>
                    <p class="text-sm text-blue-600">Hitung denda keterlambatan</p>
                </div>
            </div>

            <form id="returnForm" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="tanggal_pengembalian" id="return_date_input">

                {{-- Info Peminjaman --}}
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500 text-xs uppercase">Tanggal Kembali</span>
                            <p class="font-semibold text-gray-900" id="return_tanggal_kembali">-</p>
                        </div>
                        <div>
                            <span class="text-gray-500 text-xs uppercase">Tanggal Pengembalian</span>
                            <input type="date" id="return_tanggal_pengembalian" name="tanggal_pengembalian_input"
                                value="{{ date('Y-m-d') }}"
                                class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                    </div>
                </div>

                {{-- Loading Indicator --}}
                <div id="returnLoading" class="hidden flex items-center justify-center py-8">
                    <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>

                {{-- Denda Info --}}
                <div id="dendaInfo" class="hidden bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                        <span class="font-semibold text-yellow-800">Informasi Denda</span>
                    </div>
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <p class="text-xs text-yellow-600 uppercase">Terlambat</p>
                            <p class="text-lg font-bold text-yellow-800" id="return_hari_terlambat">0</p>
                            <p class="text-xs text-yellow-500">hari</p>
                        </div>
                        <div>
                            <p class="text-xs text-yellow-600 uppercase">Denda/Hari</p>
                            <p class="text-lg font-bold text-yellow-800">Rp 5.000</p>
                        </div>
                        <div>
                            <p class="text-xs text-yellow-600 uppercase">Total Denda</p>
                            <p class="text-2xl font-bold text-red-600" id="return_total_denda">Rp 0</p>
                        </div>
                    </div>
                    <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-700">
                            <i class="fas fa-info-circle mr-1"></i>
                            Setelah pengembalian, user dapat membayar denda melalui link pembayaran yang akan dikirim atau melalui halaman peminjaman mereka.
                        </p>
                    </div>
                </div>

                {{-- No Denda Message --}}
                <div id="noDendaInfo" class="hidden bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center text-green-700">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span class="font-medium">Pengembalian tepat waktu! Tidak ada denda.</span>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeReturnModal()"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" id="returnSubmitBtn"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium shadow-sm transition">
                        <i class="fas fa-check mr-1"></i>
                        Konfirmasi Pengembalian
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================== --}}
    {{-- JAVASCRIPT --}}
    {{-- ========================================================== --}}
    @push('scripts')
        <script>
            let currentPeminjamanId = null;

            // Reject Modal Functions
            function showRejectModal(id) {
                currentPeminjamanId = id;
                document.getElementById('rejectForm').action = `/admin/peminjaman/${id}/reject`;
                document.getElementById('rejectModal').classList.remove('hidden');
                document.getElementById('rejection_notes').value = '';
                document.getElementById('rejection_reason').value = '';
            }

            function closeRejectModal() {
                document.getElementById('rejectModal').classList.add('hidden');
                currentPeminjamanId = null;
            }

            // Handle Reject Form Submit
            document.getElementById('rejectForm').addEventListener('submit', function (e) {
                const reason = document.getElementById('rejection_notes').value.trim();
                if (!reason) {
                    e.preventDefault();
                    alert('Alasan penolakan wajib diisi!');
                    document.getElementById('rejection_notes').focus();
                } else {
                    document.getElementById('rejection_reason').value = reason;
                }
            });

            // Return Modal Functions
            function showReturnModal(id, tanggalKembali) {
                currentPeminjamanId = id;
                document.getElementById('returnForm').action = `/admin/peminjaman/${id}/return`;
                document.getElementById('return_tanggal_kembali').textContent = formatDate(tanggalKembali);
                document.getElementById('returnModal').classList.remove('hidden');

                // Calculate initial denda
                calculateDendaReturn();
            }

            function closeReturnModal() {
                document.getElementById('returnModal').classList.add('hidden');
                currentPeminjamanId = null;
            }

            function formatDate(dateString) {
                if (!dateString) return '-';
                const date = new Date(dateString);
                const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
                return date.toLocaleDateString('id-ID', options);
            }

            function calculateDendaReturn() {
                if (!currentPeminjamanId) return;

                const tanggalPengembalian = document.getElementById('return_tanggal_pengembalian').value;
                if (!tanggalPengembalian) return;

                // Show loading
                document.getElementById('returnLoading').classList.remove('hidden');
                document.getElementById('dendaInfo').classList.add('hidden');
                document.getElementById('noDendaInfo').classList.add('hidden');

                fetch('/admin/api/calculate-denda', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        peminjaman_id: currentPeminjamanId,
                        tanggal_pengembalian: tanggalPengembalian
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('returnLoading').classList.add('hidden');

                        if (data.success) {
                            document.getElementById('return_date_input').value = tanggalPengembalian;
                            document.getElementById('return_hari_terlambat').textContent = data.hari_terlambat;
                            document.getElementById('return_total_denda').textContent = data.formatted_denda;

                            if (data.denda > 0) {
                                document.getElementById('dendaInfo').classList.remove('hidden');
                                document.getElementById('returnSubmitBtn').innerHTML = `<i class="fas fa-check mr-1"></i> Konfirmasi (Denda: ${data.formatted_denda})`;
                            } else {
                                document.getElementById('noDendaInfo').classList.remove('hidden');
                                document.getElementById('returnSubmitBtn').innerHTML = '<i class="fas fa-check mr-1"></i> Konfirmasi Pengembalian';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.getElementById('returnLoading').classList.add('hidden');
                    });
            }

            // Calculate denda when date changes
            document.getElementById('return_tanggal_pengembalian').addEventListener('change', calculateDendaReturn);

            // Confirm Denda Paid
            function confirmDendaPaid(id) {
                if (confirm('Apakah Anda yakin denda ini sudah dibayar oleh user?')) {
                    fetch(`/admin/peminjaman/${id}/confirm-denda`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                                location.reload();
                            } else {
                                alert(data.message || 'Gagal mengkonfirmasi');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan');
                        });
                }
            }

            // Close modals when clicking outside
            document.getElementById('rejectModal').addEventListener('click', function (e) {
                if (e.target === this) closeRejectModal();
            });

            document.getElementById('returnModal').addEventListener('click', function (e) {
                if (e.target === this) closeReturnModal();
            });

            // Close on Escape key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeRejectModal();
                    closeReturnModal();
                }
            });
        </script>
    @endpush
</x-app-layout>