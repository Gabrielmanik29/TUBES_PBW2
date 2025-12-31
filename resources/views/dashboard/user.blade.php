<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    <i class="fas fa-home mr-2 text-blue-600"></i>
                    Dashboard Saya
                </h2>
                <p class="text-sm text-gray-500 mt-1">Selamat datang, {{ Auth::user()->name }}!</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">
                    <i class="fas fa-user mr-1"></i>
                    Anggota
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Peminjaman Aktif -->
                <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 hover:shadow-xl transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-handshake text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Peminjaman Aktif</p>
                                <p class="text-3xl font-bold text-gray-800">{{ $countPeminjamanAktif }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm">
                            @if($countTerlambat > 0)
                            <span class="text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $countTerlambat }} melewati batas
                            </span>
                            @else
                            <span class="text-green-600 flex items-center">
                                <i class="fas fa-check-circle mr-1"></i>
                                Semua aman
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Total Peminjaman -->
                <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 hover:shadow-xl transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-history text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Peminjaman</p>
                                <p class="text-3xl font-bold text-gray-800">{{ $totalPeminjaman }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm">
                            <span class="text-green-600">
                                <i class="fas fa-check-double mr-1"></i>
                                {{ $peminjamanSelesai }} selesai
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Tagihan Denda -->
                <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 hover:shadow-xl transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Tagihan Denda</p>
                                <p class="text-3xl font-bold text-gray-800">Rp {{ number_format($totalTagihanDenda, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        @if($tagihanDenda->count() > 0)
                        <div class="mt-4 flex items-center text-sm">
                            <span class="text-red-600">
                                <i class="fas fa-receipt mr-1"></i>
                                {{ $tagihanDenda->count() }} tagihan aktif
                            </span>
                        </div>
                        @else
                        <div class="mt-4 flex items-center text-sm">
                            <span class="text-green-600">
                                <i class="fas fa-check-circle mr-1"></i>
                                Tidak ada denda
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 overflow-hidden shadow-lg rounded-xl">
                    <div class="p-6 h-full flex flex-col justify-center">
                        <a href="{{ route('items.index') }}" class="flex items-center justify-center w-full py-3 bg-white/20 rounded-lg hover:bg-white/30 transition mb-3">
                            <i class="fas fa-plus-circle text-white mr-2"></i>
                            <span class="text-white font-medium">Pinjam Barang</span>
                        </a>
                        <a href="{{ route('my.borrowings') }}" class="flex items-center justify-center w-full py-3 bg-white/20 rounded-lg hover:bg-white/30 transition">
                            <i class="fas fa-list text-white mr-2"></i>
                            <span class="text-white font-medium">Riwayat</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Alert for Late Returns -->
            @if($countTerlambat > 0)
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">
                            Perhatian! Anda memiliki <strong>{{ $countTerlambat }}</strong> peminjaman yang melewati batas waktu pengembalian.
                            Harap segera kembalikan barang untuk menghindari denda tambahan.
                        </p>
                    </div>
                    <div class="ml-auto">
                        <a href="{{ route('my.borrowings') }}?status=disetujui" class="text-sm text-red-600 hover:text-red-800 font-medium">
                            Lihat detail <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Peminjaman Aktif -->
                <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-handshake text-blue-500 mr-2"></i>
                            Peminjaman Aktif
                        </h3>
                        <a href="{{ route('my.borrowings') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            Lihat semua <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="p-6">
                        @if($peminjamanAktif->isEmpty())
                        <div class="text-center py-8">
                            <i class="fas fa-box-open text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500 mb-4">Anda belum memiliki peminjaman aktif</p>
                            <a href="{{ route('items.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                <i class="fas fa-plus mr-2"></i>
                                Pinjam Barang
                            </a>
                        </div>
                        @else
                        <div class="space-y-4">
                            @foreach($peminjamanAktif as $peminjaman)
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 hover:bg-gray-100 transition">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-white rounded-lg shadow flex items-center justify-center mr-4">
                                            <i class="fas fa-box text-blue-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $peminjaman->item->name }}</p>
                                            <p class="text-sm text-gray-500">
                                                {{ $peminjaman->quantity }} unit •
                                                {{ $peminjaman->tanggal_pinjam->format('d M') }} - {{ $peminjaman->tanggal_kembali->format('d M Y') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @switch($peminjaman->status)
                                        @case('diajukan')
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">
                                            Menunggu
                                        </span>
                                        @break
                                        @case('disetujui')
                                        @if($peminjaman->tanggal_kembali < now())
                                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                                            Terlambat
                                        </span>
                                        @else
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                            Aktif
                                        </span>
                                        @endif
                                        @break
                                        @endswitch
                                    </div>
                                </div>

                                @if($peminjaman->tanggal_kembali < now())
                                <div class="mt-3 p-3 bg-red-50 rounded-lg">
                                    <p class="text-sm text-red-700">
                                        <i class="fas fa-clock mr-1"></i>
                                        Batas pengembalian: {{ $peminjaman->tanggal_kembali->format('d M Y') }}
                                        <span class="font-medium ml-1">
                                            ({{ $peminjaman->tanggal_kembali->diffInDays(now()) }} hari terlambat)
                                        </span>
                                    </p>
                                    <p class="text-sm text-red-600 mt-1">
                                        Denda: Rp {{ number_format($peminjaman->hitungDenda(), 0, ',', '.') }}
                                    </p>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Tagihan Denda -->
                <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-receipt text-red-500 mr-2"></i>
                            Tagihan Denda
                        </h3>
                        @if($tagihanDenda->count() > 0)
                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-medium">
                            {{ $tagihanDenda->count() }} tagihan
                        </span>
                        @endif
                    </div>
                    <div class="p-6">
                        @if($tagihanDenda->isEmpty())
                        <div class="text-center py-8">
                            <i class="fas fa-check-circle text-green-400 text-4xl mb-3"></i>
                            <p class="text-gray-500">Tidak ada tagihan denda</p>
                            <p class="text-sm text-gray-400 mt-1">Great! Anda tidak memiliki denda yang belum dibayar</p>
                        </div>
                        @else
                        <div class="space-y-4">
                            @foreach($tagihanDenda as $tagihan)
                            <div class="p-4 bg-red-50 rounded-lg border border-red-100">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-white rounded-lg shadow flex items-center justify-center mr-3">
                                            <i class="fas fa-box text-red-500"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $tagihan->item->name }}</p>
                                            <p class="text-sm text-gray-500">
                                                Dikembalikan: {{ $tagihan->tanggal_pengembalian_aktual->format('d M Y') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xl font-bold text-red-600">Rp {{ number_format($tagihan->denda, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between mt-3">
                                    <span class="text-xs text-gray-500">
                                        Status:
                                        @if($tagihan->denda_dibayar)
                                        <span class="text-green-600 font-medium">Sudah dibayar</span>
                                        @else
                                        <span class="text-red-600 font-medium">Belum dibayar</span>
                                        @endif
                                    </span>
                                    @if(!$tagihan->denda_dibayar)
                                    <button class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 transition">
                                        <i class="fas fa-credit-card mr-1"></i>
                                        Bayar Sekarang
                                    </button>
                                    @endif
                                </div>
                            </div>
                            @endforeach

                            <!-- Total Tagihan -->
                            <div class="p-4 bg-gray-800 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <span class="text-white font-medium">Total Tagihan</span>
                                    <span class="text-2xl font-bold text-white">Rp {{ number_format($totalTagihanDenda, 0, ',', '.') }}</span>
                                </div>
                                @if($totalTagihanDenda > 0)
                                <button class="w-full mt-4 py-3 bg-green-500 text-white rounded-lg font-medium hover:bg-green-600 transition">
                                    <i class="fas fa-credit-card mr-2"></i>
                                    Bayar Semua via Midtrans
                                </button>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Riwayat Peminjaman -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-history text-gray-500 mr-2"></i>
                        Riwayat Peminjaman
                    </h3>
                    <a href="{{ route('my.borrowings') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        Lihat lengkap <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="p-6">
                    @if($riwayatPeminjaman->isEmpty())
                    <div class="text-center py-8">
                        <i class="fas fa-history text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">Belum ada riwayat peminjaman</p>
                    </div>
                    @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pinjam</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Kembali</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Denda</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($riwayatPeminjaman as $peminjaman)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                                <i class="fas fa-box text-gray-500"></i>
                                            </div>
                                            <div class="text-sm font-medium text-gray-800">{{ $peminjaman->item->name }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $peminjaman->tanggal_pinjam->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $peminjaman->tanggal_kembali->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @switch($peminjaman->status)
                                        @case('dikembalikan')
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                            <i class="fas fa-check mr-1"></i>Selesai
                                        </span>
                                        @break
                                        @case('dibatalkan')
                                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">
                                            <i class="fas fa-times mr-1"></i>Dibatalkan
                                        </span>
                                        @break
                                        @case('ditolak')
                                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                                            <i class="fas fa-ban mr-1"></i>Ditolak
                                        </span>
                                        @break
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($peminjaman->denda > 0)
                                        <span class="text-red-600 font-medium">
                                            Rp {{ number_format($peminjaman->denda, 0, ',', '.') }}
                                            @if($peminjaman->denda_dibayar)
                                            <span class="text-green-500 text-xs ml-1">
                                                <i class="fas fa-check-circle"></i>
                                            </span>
                                            @endif
                                        </span>
                                        @else
                                        <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

