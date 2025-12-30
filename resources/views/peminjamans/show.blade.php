<!-- resources/views/peminjaman/show.blade.php -->
@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Kembali ke halaman sebelumnya -->
        <a href="{{ route('my.borrowings') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-6">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Riwayat Peminjaman
        </a>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <!-- Header -->
            <div class="px-4 py-5 sm:px-6 border-b">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Detail Peminjaman #{{ $peminjaman->id }}
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Informasi lengkap peminjaman barang
                </p>
            </div>

            <!-- Content -->
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Informasi Barang -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-700 mb-2">Informasi Barang</h4>
                        <p><strong>Nama:</strong> {{ $peminjaman->item->name }}</p>
                        <p><strong>Kategori:</strong> {{ $peminjaman->item->category->name }}</p>
                        <p><strong>Jumlah:</strong> {{ $peminjaman->quantity }} unit</p>
                    </div>

                    <!-- Informasi Peminjaman -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-700 mb-2">Informasi Peminjaman</h4>
                        <p><strong>Tanggal Pinjam:</strong> {{ $peminjaman->tanggal_pinjam->format('d F Y') }}</p>
                        <p><strong>Tanggal Kembali:</strong> {{ $peminjaman->tanggal_kembali->format('d F Y') }}</p>
                        <p><strong>Status:</strong> 
                            <span class="px-2 py-1 text-xs rounded {{ $statusClasses[$peminjaman->status] ?? 'bg-gray-100' }}">
                                {{ ucfirst($peminjaman->status) }}
                            </span>
                        </p>
                    </div>

                    <!-- Informasi Denda -->
                    @if($peminjaman->denda > 0)
                    <div class="bg-red-50 p-4 rounded-lg md:col-span-2">
                        <h4 class="font-medium text-red-700 mb-2">Informasi Denda</h4>
                        <p><strong>Total Denda:</strong> Rp {{ number_format($peminjaman->denda, 0, ',', '.') }}</p>
                        <p><strong>Status Pembayaran:</strong> 
                            {{ $peminjaman->denda_dibayar ? 'Lunas' : 'Belum Dibayar' }}
                        </p>
                        @if($peminjaman->hariKeterlambatan() > 0)
                            <p><strong>Keterlambatan:</strong> {{ $peminjaman->hariKeterlambatan() }} hari</p>
                        @endif
                    </div>
                    @endif

                    <!-- Informasi Pengembalian -->
                    @if($peminjaman->tanggal_pengembalian_aktual)
                    <div class="bg-blue-50 p-4 rounded-lg md:col-span-2">
                        <h4 class="font-medium text-blue-700 mb-2">Informasi Pengembalian</h4>
                        <p><strong>Tanggal Dikembalikan:</strong> {{ $peminjaman->tanggal_pengembalian_aktual->format('d F Y') }}</p>
                        <p><strong>Keterlambatan:</strong> 
                            @if($peminjaman->isTerlambat())
                                {{ $peminjaman->hariKeterlambatan() }} hari
                            @else
                                Tepat waktu
                            @endif
                        </p>
                    </div>
                    @endif
                </div>

                <!-- Tombol Aksi -->
                <div class="mt-6 flex justify-end space-x-3">
                    @if($peminjaman->status === 'diajukan')
                    <form action="{{ route('peminjaman.cancel', $peminjaman) }}" method="POST" 
                          onsubmit="return confirm('Yakin ingin membatalkan peminjaman?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                            Batalkan Peminjaman
                        </button>
                    </form>
                    @endif

                    @if($peminjaman->denda > 0 && !$peminjaman->denda_dibayar)
                    <form action="{{ route('payment.pay', $peminjaman) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                            Bayar Denda
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection