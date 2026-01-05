<<<<<<< HEAD
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
=======
<x-app-layout>
    @push('styles')
        <style>
            .status-pill { padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem; }
            .card-hover:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        </style>
    @endpush

    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
            <i class="fas fa-list-alt text-indigo-600"></i>
            {{ __('Peminjaman Saya') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group card-hover transition duration-300">
                    <div class="absolute right-0 top-0 h-full w-1 bg-indigo-500 rounded-r-2xl"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Riwayat</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $totalPeminjaman }}</h3>
                        </div>
                        <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600">
                            <i class="fas fa-history"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group card-hover transition duration-300">
                    <div class="absolute right-0 top-0 h-full w-1 {{ $totalDenda > 0 ? 'bg-red-500' : 'bg-gray-200' }} rounded-r-2xl"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tagihan Denda</p>
                            <h3 class="text-2xl font-bold {{ $totalDenda > 0 ? 'text-red-600' : 'text-gray-800' }} mt-1">
                                Rp {{ number_format($totalDenda, 0, ',', '.') }}
                            </h3>
                            @if($totalDenda > 0)
                                <p class="text-xs text-red-500 mt-1 font-medium animate-pulse">Segera Bayar!</p>
                            @else
                                <p class="text-xs text-green-500 mt-1 flex items-center gap-1"><i class="fas fa-check"></i> Aman</p>
                            @endif
                        </div>
                        <div class="p-3 {{ $totalDenda > 0 ? 'bg-red-50 text-red-600' : 'bg-gray-50 text-gray-400' }} rounded-xl">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group card-hover transition duration-300">
                    <div class="absolute right-0 top-0 h-full w-1 bg-emerald-500 rounded-r-2xl"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Sedang Dipinjam</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1">
                                {{ auth()->user()->peminjamans()->where('status', 'disetujui')->count() }}
                            </h3>
                        </div>
                        <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
                            <i class="fas fa-box-open"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group card-hover transition duration-300">
                    <div class="absolute right-0 top-0 h-full w-1 bg-yellow-500 rounded-r-2xl"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Menunggu</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1">
                                {{ auth()->user()->peminjamans()->where('status', 'diajukan')->count() }}
                            </h3>
                        </div>
                        <div class="p-3 bg-yellow-50 rounded-xl text-yellow-600">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>

            @if($totalDenda > 0)
                <div class="bg-red-50 border-l-4 border-red-500 rounded-r-xl p-4 mb-8 shadow-sm flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl mt-0.5"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-bold text-red-800">Perhatian: Anda memiliki tunggakan denda!</h3>
                        <div class="mt-1 text-sm text-red-700">
                            Total denda sebesar <strong>Rp {{ number_format($totalDenda, 0, ',', '.') }}</strong>. 
                            Harap segera hubungi admin atau lakukan pembayaran agar akun Anda tidak dibekukan.
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                
                <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-history text-indigo-500"></i> Riwayat Transaksi
                    </h3>

                    <form method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                        <div class="relative w-full sm:w-64">
                            <select name="status" onchange="this.form.submit()"
                                class="w-full pl-10 pr-4 py-2 border-gray-300 rounded-xl text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition">
                                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Semua Status</option>
                                <option value="diajukan" {{ $status == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                                <option value="disetujui" {{ $status == 'disetujui' ? 'selected' : '' }}>Sedang Dipinjam</option>
                                <option value="dikembalikan" {{ $status == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                                <option value="ditolak" {{ $status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                <option value="dibatalkan" {{ $status == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-filter text-gray-400"></i>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold tracking-wider">
                            <tr>
                                <th class="px-6 py-4 text-left">Barang</th>
                                <th class="px-6 py-4 text-left">Jadwal</th>
                                <th class="px-6 py-4 text-left">Status</th>
                                <th class="px-6 py-4 text-left">Denda & Info</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($peminjamans as $peminjaman)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="h-12 w-12 flex-shrink-0 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center overflow-hidden">
                                                @if($peminjaman->item->photo)
                                                    <img src="{{ asset('storage/' . $peminjaman->item->photo) }}" class="h-full w-full object-cover">
                                                @else
                                                    <i class="fas fa-box text-gray-400"></i>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900">{{ $peminjaman->item->name }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $peminjaman->item->category->name ?? 'Umum' }} • Qty: {{ $peminjaman->quantity }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 flex items-center gap-2">
                                            <i class="fas fa-arrow-up text-emerald-500 text-xs"></i> 
                                            {{ $peminjaman->tanggal_pinjam->format('d M Y') }}
                                        </div>
                                        <div class="text-sm text-gray-500 flex items-center gap-2 mt-1">
                                            <i class="fas fa-arrow-down text-red-400 text-xs"></i>
                                            {{ $peminjaman->tanggal_kembali->format('d M Y') }}
                                        </div>
                                        @if($peminjaman->status == 'disetujui' && $peminjaman->tanggal_kembali->isPast())
                                            <span class="text-xs text-red-600 font-bold mt-1 block bg-red-50 px-2 py-0.5 rounded w-fit">
                                                Telat {{ $peminjaman->tanggal_kembali->diffInDays(now()) }} Hari
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusConfig = [
                                                'diajukan' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'fa-clock'],
                                                'disetujui' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'icon' => 'fa-check'],
                                                'ditolak' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'fa-times'],
                                                'dikembalikan' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-box-open'],
                                                'dibatalkan' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-ban'],
                                            ];
                                            $config = $statusConfig[$peminjaman->status] ?? $statusConfig['dibatalkan'];
                                        @endphp
                                        <span class="status-pill {{ $config['bg'] }} {{ $config['text'] }}">
                                            <i class="fas {{ $config['icon'] }}"></i> {{ ucfirst($peminjaman->status) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($peminjaman->denda > 0)
                                            <div class="flex flex-col items-start">
                                                <span class="text-sm font-bold text-red-600">Rp {{ number_format($peminjaman->denda, 0, ',', '.') }}</span>
                                                @if($peminjaman->denda_dibayar)
                                                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full mt-1">Lunas</span>
                                                @else
                                                    <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full mt-1 animate-pulse">Belum Lunas</span>
                                                @endif
                                            </div>
                                        @elseif($peminjaman->status == 'ditolak')
                                            <span class="text-xs text-red-500 italic max-w-xs truncate block" title="{{ $peminjaman->rejection_reason }}">
                                                "{{ Str::limit($peminjaman->rejection_reason ?? 'Tidak ada alasan', 20) }}"
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-sm">-</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if($peminjaman->status == 'diajukan')
                                            <form action="{{ route('peminjaman.cancel', $peminjaman) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Batalkan peminjaman ini?')" 
                                                    class="text-red-600 hover:text-red-900 hover:bg-red-50 px-3 py-1.5 rounded-lg transition">
                                                    Batalkan
                                                </button>
                                            </form>
                                        @elseif($peminjaman->denda > 0 && !$peminjaman->denda_dibayar)
                                            <a href="{{ route('denda.detail', $peminjaman) }}" 
                                                class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition text-xs font-bold shadow-sm">
                                                <i class="fas fa-credit-card mr-1"></i> Bayar Sekarang
                                            </a>
                                        @elseif($peminjaman->denda > 0 && $peminjaman->denda_dibayar)
                                            <span class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-xs font-bold">
                                                <i class="fas fa-check-circle mr-1"></i> Lunas
                                            </span>
                                        @else
                                            <a href="{{ route('items.show', $peminjaman->item) }}" class="text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 px-3 py-1.5 rounded-lg transition">
                                                Lihat Barang
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4 text-gray-400">
                                                <i class="fas fa-folder-open text-2xl"></i>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900">Belum ada riwayat</h3>
                                            <p class="text-gray-500 mt-1 mb-6">Anda belum melakukan peminjaman apapun.</p>
                                            <a href="{{ route('items.index') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium shadow-lg shadow-indigo-200 transition">
                                                Mulai Meminjam
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($peminjamans->hasPages())
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                        {{ $peminjamans->links() }}
                    </div>
                @endif
            </div>

            <div class="mt-6 flex flex-wrap gap-4 justify-center text-xs text-gray-500">
                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-yellow-500 mr-2"></span> Diajukan</div>
                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span> Disetujui</div>
                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span> Dikembalikan</div>
                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span> Ditolak/Denda</div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            // Auto-hide flash messages
            setTimeout(function () {
                const flashMessages = document.querySelectorAll('.bg-green-100, .bg-red-100');
                flashMessages.forEach(function (message) {
                    message.style.transition = 'opacity 0.5s ease-out';
                    message.style.opacity = '0';
                    setTimeout(() => message.remove(), 500);
                });
            }, 5000);
        </script>
    @endpush
</x-app-layout>
>>>>>>> d55b9af1f343e0e3324d653f7222d76df8c70cd2
