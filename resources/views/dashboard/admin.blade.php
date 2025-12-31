<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    <i class="fas fa-tachometer-alt mr-2 text-purple-600"></i>
                    Dashboard Admin
                </h2>
                <p class="text-sm text-gray-500 mt-1">Selamat datang, {{ Auth::user()->name }}!</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-medium">
                    <i class="fas fa-shield-alt mr-1"></i>
                    Administrator
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Items -->
                <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 hover:shadow-xl transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-box text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Barang</p>
                                <p class="text-3xl font-bold text-gray-800">{{ $stats['total_items'] }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm">
                            <span class="text-blue-600">
                                <i class="fas fa-cubes mr-1"></i>
                                {{ $stats['total_categories'] }} kategori
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Total Users -->
                <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 hover:shadow-xl transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-users text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Anggota</p>
                                <p class="text-3xl font-bold text-gray-800">{{ $stats['total_users'] }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm">
                            <span class="text-green-600">
                                <i class="fas fa-user-plus mr-1"></i>
                                Terdaftar
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Total Peminjaman -->
                <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 hover:shadow-xl transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="w-14 h-14 bg-orange-100 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-handshake text-orange-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Peminjaman</p>
                                <p class="text-3xl font-bold text-gray-800">{{ $stats['total_peminjaman'] }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm">
                            <span class="text-orange-600">
                                <i class="fas fa-history mr-1"></i>
                                Semua waktu
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Total Denda -->
                <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 hover:shadow-xl transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-money-bill-wave text-red-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Denda Pending</p>
                                <p class="text-3xl font-bold text-gray-800">Rp {{ number_format($totalDendaBelumDibayar, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm">
                            <span class="text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                Belum dibayar
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert for Late Returns -->
            @if($peminjamanTerlambat > 0)
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">
                            Perhatian! Ada <strong>{{ $peminjamanTerlambat }}</strong> peminjaman yang melewati batas waktu dan belum dikembalikan.
                        </p>
                    </div>
                    <div class="ml-auto">
                        <a href="#" class="text-sm text-red-600 hover:text-red-800 font-medium">
                            Lihat detail <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Peminjaman Menunggu Persetujuan -->
                <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-clock text-yellow-500 mr-2"></i>
                            Menunggu Persetujuan
                        </h3>
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-medium">
                            {{ $peminjamanMenunggu->count() }} pengajuan
                        </span>
                    </div>
                    <div class="p-6">
                        @if($peminjamanMenunggu->isEmpty())
                        <div class="text-center py-8">
                            <i class="fas fa-check-circle text-green-400 text-4xl mb-3"></i>
                            <p class="text-gray-500">Tidak ada peminjaman yang menunggu</p>
                        </div>
                        @else
                        <div class="space-y-4">
                            @foreach($peminjamanMenunggu as $peminjaman)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-blue-600 font-semibold">{{ strtoupper(substr($peminjaman->user->name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $peminjaman->user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $peminjaman->item->name }} ({{ $peminjaman->quantity }} unit)</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <form action="#" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1 bg-green-500 text-white rounded-lg text-sm hover:bg-green-600 transition">
                                            <i class="fas fa-check mr-1"></i> Setuju
                                        </button>
                                    </form>
                                    <form action="#" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded-lg text-sm hover:bg-red-600 transition">
                                            <i class="fas fa-times mr-1"></i> Tolak
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @if($peminjamanMenunggu->count() >= 5)
                        <div class="mt-4 text-center">
                            <a href="#" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                Lihat semua pengajuan <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>

                <!-- Pengembalian Menunggu Konfirmasi -->
                <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-undo text-purple-500 mr-2"></i>
                            Konfirmasi Pengembalian
                        </h3>
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-medium">
                            {{ $pengembalianMenunggu->count() }} barang
                        </span>
                    </div>
                    <div class="p-6">
                        @if($pengembalianMenunggu->isEmpty())
                        <div class="text-center py-8">
                            <i class="fas fa-check-circle text-green-400 text-4xl mb-3"></i>
                            <p class="text-gray-500">Tidak ada pengembalian yang menunggu</p>
                        </div>
                        @else
                        <div class="space-y-4">
                            @foreach($pengembalianMenunggu as $peminjaman)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-purple-600 font-semibold">{{ strtoupper(substr($peminjaman->user->name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $peminjaman->user->name }}</p>
                                        <p class="text-sm text-gray-500">
                                            {{ $peminjaman->item->name }}
                                            @if($peminjaman->denda > 0)
                                            <span class="text-red-500 ml-2">
                                                <i class="fas fa-exclamation-circle mr-1"></i>
                                                Denda: Rp {{ number_format($peminjaman->denda, 0, ',', '.') }}
                                            </span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <form action="#" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1 bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600 transition">
                                        <i class="fas fa-check-double mr-1"></i> Konfirmasi
                                    </button>
                                </form>
                            </div>
                            @endforeach
                        </div>
                        @if($pengembalianMenunggu->count() >= 5)
                        <div class="mt-4 text-center">
                            <a href="#" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                Lihat semua <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-history text-gray-500 mr-2"></i>
                        Aktivitas Terbaru
                    </h3>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Denda</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentPeminjaman as $peminjaman)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-gray-600 font-semibold text-sm">{{ strtoupper(substr($peminjaman->user->name, 0, 1)) }}</span>
                                            </div>
                                            <div class="text-sm font-medium text-gray-800">{{ $peminjaman->user->name }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $peminjaman->item->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $peminjaman->tanggal_pinjam->format('d/m/Y') }} - {{ $peminjaman->tanggal_kembali->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @switch($peminjaman->status)
                                        @case('diajukan')
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Diajukan</span>
                                        @break
                                        @case('disetujui')
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">Disetujui</span>
                                        @break
                                        @case('dikembalikan')
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Dikembalikan</span>
                                        @break
                                        @case('ditolak')
                                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">Ditolak</span>
                                        @break
                                        @case('dibatalkan')
                                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">Dibatalkan</span>
                                        @break
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        @if($peminjaman->denda > 0)
                                        <span class="text-red-600 font-medium">Rp {{ number_format($peminjaman->denda, 0, ',', '.') }}</span>
                                        @else
                                        <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-gradient-to-r from-purple-600 to-blue-600 overflow-hidden shadow-lg rounded-xl">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">
                        <i class="fas fa-bolt mr-2"></i>
                        Quick Actions
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="#" class="flex flex-col items-center p-4 bg-white/10 rounded-lg hover:bg-white/20 transition">
                            <i class="fas fa-plus-circle text-white text-2xl mb-2"></i>
                            <span class="text-white text-sm font-medium">Tambah Barang</span>
                        </a>
                        <a href="#" class="flex flex-col items-center p-4 bg-white/10 rounded-lg hover:bg-white/20 transition">
                            <i class="fas fa-tags text-white text-2xl mb-2"></i>
                            <span class="text-white text-sm font-medium">Kelola Kategori</span>
                        </a>
                        <a href="#" class="flex flex-col items-center p-4 bg-white/10 rounded-lg hover:bg-white/20 transition">
                            <i class="fas fa-chart-bar text-white text-2xl mb-2"></i>
                            <span class="text-white text-sm font-medium">Lihat Laporan</span>
                        </a>
                        <a href="#" class="flex flex-col items-center p-4 bg-white/10 rounded-lg hover:bg-white/20 transition">
                            <i class="fas fa-users text-white text-2xl mb-2"></i>
                            <span class="text-white text-sm font-medium">Kelola User</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

