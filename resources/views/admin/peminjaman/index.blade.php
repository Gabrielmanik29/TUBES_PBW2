{{-- resources/views/admin/peminjaman/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Kelola Peminjaman')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">

                {{-- FLASH MESSAGE --}}
                @if(session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
                        <ul class="list-disc pl-5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- HEADER --}}
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Kelola Peminjaman</h2>

                    <div class="flex items-center gap-2">
                        {{-- kalau belum punya export, biarkan # --}}
                        <a href="#"
                           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                            Export Excel
                        </a>
                    </div>
                </div>

                {{-- FILTER --}}
                <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <form method="GET" action="{{ route('admin.peminjaman') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        {{-- Search --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Nama barang / peminjam"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                <option value="">Semua Status</option>
                                <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                                <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                            </select>
                        </div>

                        {{-- Dari --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                            <input type="date"
                                   name="start_date"
                                   value="{{ request('start_date') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>

                        {{-- Sampai --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                            <input type="date"
                                   name="end_date"
                                   value="{{ request('end_date') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>

                        {{-- Buttons --}}
                        <div class="flex items-end gap-2 md:col-span-5">
                            <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                                Filter
                            </button>

                            <a href="{{ route('admin.peminjaman') }}"
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                {{-- STATS (opsional) --}}
                @php
                    $totalAll = $peminjamans->total();
                    $countDiajukan = \App\Models\Peminjaman::where('status', 'diajukan')->count();
                    $countDisetujui = \App\Models\Peminjaman::where('status', 'disetujui')->count();
                    $countDitolak = \App\Models\Peminjaman::where('status', 'ditolak')->count();
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <div class="text-blue-800 font-semibold">Total Peminjaman</div>
                        <div class="text-2xl font-bold text-blue-600">{{ $totalAll }}</div>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <div class="text-yellow-800 font-semibold">Menunggu</div>
                        <div class="text-2xl font-bold text-yellow-600">{{ $countDiajukan }}</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                        <div class="text-green-800 font-semibold">Disetujui</div>
                        <div class="text-2xl font-bold text-green-600">{{ $countDisetujui }}</div>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                        <div class="text-red-800 font-semibold">Ditolak</div>
                        <div class="text-2xl font-bold text-red-600">{{ $countDitolak }}</div>
                    </div>
                </div>

                {{-- TABLE --}}
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peminjam</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
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

                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        #{{ $peminjaman->id }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ optional($peminjaman->user)->name ?? '-' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ optional($peminjaman->user)->email ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ optional($peminjaman->item)->name ?? '-' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ optional(optional($peminjaman->item)->category)->name ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $peminjaman->quantity }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div>Pinjam:
                                            {{ $peminjaman->tanggal_pinjam?->format('d/m/Y') ?? '-' }}
                                        </div>
                                        <div>Kembali:
                                            {{ $peminjaman->tanggal_kembali?->format('d/m/Y') ?? '-' }}
                                        </div>

                                        @if(!empty($peminjaman->tanggal_pengembalian_aktual))
                                            <div class="text-xs text-gray-500 mt-1">
                                                Aktual: {{ $peminjaman->tanggal_pengembalian_aktual?->format('d/m/Y') }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $badge }}">
                                            {{ ucfirst($peminjaman->status) }}
                                        </span>

                                        @if(!empty($peminjaman->rejection_reason))
                                            <div class="text-xs text-gray-600 mt-1">
                                                Alasan: {{ $peminjaman->rejection_reason }}
                                            </div>
                                        @endif

                                        @if(!empty($peminjaman->denda) && $peminjaman->denda > 0)
                                            <div class="text-xs text-red-600 mt-1">
                                                Denda: Rp {{ number_format($peminjaman->denda, 0, ',', '.') }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        {{-- AKSI BERDASARKAN STATUS --}}
                                        @if($peminjaman->status === 'diajukan')
                                            <div class="flex gap-2">
                                                {{-- APPROVE --}}
                                                <form method="POST" action="{{ route('admin.peminjaman.approve', $peminjaman) }}">
                                                    @csrf
                                                    <button type="submit"
                                                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded"
                                                        onclick="return confirm('Setujui peminjaman ini?')">
                                                        Approve
                                                    </button>
                                                </form>

                                                {{-- REJECT (modal) --}}
                                                <button type="button"
                                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded"
                                                    onclick="showRejectModal({{ $peminjaman->id }})">
                                                    Reject
                                                </button>
                                            </div>

                                        @elseif($peminjaman->status === 'disetujui')
                                            {{-- RETURN --}}
                                            <form action="{{ route('admin.peminjaman.return', $peminjaman) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    onclick="return confirm('Konfirmasi pengembalian barang?')"
                                                    class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                                    Kembalikan
                                                </button>
                                            </form>

                                        @elseif($peminjaman->status === 'ditolak')
                                            <span class="text-gray-500 text-sm">Ditolak</span>
                                        @else
                                            <span class="text-gray-500 text-sm">Selesai</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        Tidak ada data peminjaman
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
<div id="rejectModal" class="fixed inset-0 bg-black/50 hidden z-50">
    <div class="bg-white rounded-lg w-full max-w-md mx-auto mt-32 p-6">
        <h3 class="text-lg font-semibold mb-4">Tolak Peminjaman</h3>

        <form id="rejectForm" method="POST">
            @csrf

            <label class="block text-sm font-medium mb-2">Alasan Penolakan</label>
            <textarea name="rejection_reason"
                required
                class="w-full border rounded p-2 mb-4"
                rows="3"
                placeholder="Tulis alasan..."></textarea>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeRejectModal()"
                    class="px-4 py-2 bg-gray-200 rounded">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded">
                    Tolak
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

    window.addEventListener('click', (e) => {
        const modal = document.getElementById('rejectModal');
        if (e.target === modal) closeRejectModal();
    });
</script>
@endpush
@endsection
