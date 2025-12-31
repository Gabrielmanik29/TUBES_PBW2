<!-- resources/views/peminjamans/my-borrowings.blade.php -->
@extends('layouts.app')

@section('title', 'Peminjaman Saya')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700">Total Peminjaman</h3>
                <p class="text-3xl font-bold text-blue-600 mt-2">{{ $totalPeminjaman }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700">Denda Belum Dibayar</h3>
                <p class="text-3xl font-bold text-red-600 mt-2">Rp {{ number_format($totalDenda, 0, ',', '.') }}</p>
                @if($totalDenda > 0)
                <a href="#" class="text-sm text-blue-600 hover:underline mt-2 inline-block">Bayar Sekarang →</a>
                @endif
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700">Sedang Dipinjam</h3>
                <p class="text-3xl font-bold text-green-600 mt-2">
                    {{ auth()->user()->peminjamans()->where('status', 'disetujui')->count() }}
                </p>
            </div>
        </div>

        <!-- Filter dan Pencarian -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <h2 class="text-xl font-bold text-gray-800">Riwayat Peminjaman</h2>

                    <div class="flex items-center space-x-4">
                        <form method="GET" class="flex items-center space-x-2">
                            <select name="status" onchange="this.form.submit()"
                                class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Semua Status</option>
                                <option value="diajukan" {{ $status == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                                <option value="disetujui" {{ $status == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                <option value="ditolak" {{ $status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                <option value="dikembalikan" {{ $status == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                                <option value="dibatalkan" {{ $status == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Peminjaman -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pinjam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Kembali</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Denda</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($peminjamans as $peminjaman)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $peminjaman->item->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $peminjaman->item->category->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $peminjaman->quantity }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $peminjaman->tanggal_pinjam->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $peminjaman->tanggal_kembali->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                $statusColors = [
                                'diajukan' => 'bg-yellow-100 text-yellow-800',
                                'disetujui' => 'bg-green-100 text-green-800',
                                'ditolak' => 'bg-red-100 text-red-800',
                                'dikembalikan' => 'bg-blue-100 text-blue-800',
                                'dibatalkan' => 'bg-gray-100 text-gray-800'
                                ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$peminjaman->status] }}">
                                    {{ ucfirst($peminjaman->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($peminjaman->denda > 0)
                                @if($peminjaman->denda_dibayar)
                                <span class="text-green-600">Rp {{ number_format($peminjaman->denda, 0, ',', '.') }} (Lunas)</span>
                                @else
                                <span class="text-red-600 font-semibold">Rp {{ number_format($peminjaman->denda, 0, ',', '.') }}</span>
                                @endif
                                @else
                                <span class="text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if($peminjaman->status == 'diajukan')
                                <form action="{{ route('peminjaman.cancel', $peminjaman) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('Yakin ingin membatalkan peminjaman?')"
                                        class="text-red-600 hover:text-red-900">
                                        Batalkan
                                    </button>
                                </form>
                                @endif

                                @if($peminjaman->denda > 0 && !$peminjaman->denda_dibayar)
                                <a href="#" class="ml-3 text-green-600 hover:text-green-900">Bayar</a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="mt-2">Belum ada riwayat peminjaman</p>
                                <a href="{{ route('items.index') }}" class="mt-4 inline-block text-blue-600 hover:underline">
                                    Pinjam barang sekarang →
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($peminjamans->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $peminjamans->links() }}
            </div>
            @endif
        </div>

        <!-- Status Legend -->
        <div class="mt-8 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Keterangan Status:</h3>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="flex items-center">
                    <span class="h-3 w-3 rounded-full bg-yellow-400 mr-2"></span>
                    <span class="text-sm">Diajukan: Menunggu persetujuan admin</span>
                </div>
                <div class="flex items-center">
                    <span class="h-3 w-3 rounded-full bg-green-400 mr-2"></span>
                    <span class="text-sm">Disetujui: Barang bisa diambil</span>
                </div>
                <div class="flex items-center">
                    <span class="h-3 w-3 rounded-full bg-red-400 mr-2"></span>
                    <span class="text-sm">Ditolak: Peminjaman tidak disetujui</span>
                </div>
                <div class="flex items-center">
                    <span class="h-3 w-3 rounded-full bg-blue-400 mr-2"></span>
                    <span class="text-sm">Dikembalikan: Barang sudah dikembalikan</span>
                </div>
                <div class="flex items-center">
                    <span class="h-3 w-3 rounded-full bg-gray-400 mr-2"></span>
                    <span class="text-sm">Dibatalkan: Dibatalkan oleh user</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-hide flash messages setelah 5 detik
    setTimeout(function() {
        const flashMessages = document.querySelectorAll('.bg-green-100, .bg-red-100');
        flashMessages.forEach(function(message) {
            message.style.transition = 'opacity 0.5s';
            message.style.opacity = '0';
            setTimeout(() => message.remove(), 500);
        });
    }, 5000);
</script>
@endpush
@endsection