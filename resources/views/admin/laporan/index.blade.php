<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Laporan Peminjaman') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.laporan.export.excel', request()->all()) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition">
                    <i class="fas fa-file-excel mr-2"></i> Export Excel
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Statistik Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-sm text-gray-500 uppercase font-bold">Total Peminjaman</p>
                    <p class="text-3xl font-black text-blue-600">{{ $stats['total_peminjaman'] }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-sm text-gray-500 uppercase font-bold">Total Denda Terkumpul</p>
                    <p class="text-3xl font-black text-red-600">Rp {{ number_format($stats['total_denda'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-sm text-gray-500 uppercase font-bold">Barang Belum Kembali</p>
                    <p class="text-3xl font-black text-orange-600">{{ $stats['barang_belum_kembali'] }}</p>
                </div>
            </div>

            {{-- Tabel Laporan --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6 text-gray-900">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="p-4 border-b font-bold text-sm">Peminjam</th>
                                <th class="p-4 border-b font-bold text-sm">Barang</th>
                                <th class="p-4 border-b font-bold text-sm">Tgl Pinjam</th>
                                <th class="p-4 border-b font-bold text-sm">Tgl Kembali</th>
                                <th class="p-4 border-b font-bold text-sm">Denda</th>
                                <th class="p-4 border-b font-bold text-sm text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($laporans as $laporan)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-4 border-b text-sm">
                                        <p class="font-bold">{{ $laporan->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $laporan->user->email }}</p>
                                    </td>
                                    <td class="p-4 border-b text-sm">{{ $laporan->item->name }}</td>
                                    <td class="p-4 border-b text-sm">{{ $laporan->tanggal_pinjam->format('d/m/Y') }}</td>
                                    <td class="p-4 border-b text-sm">{{ $laporan->tanggal_kembali->format('d/m/Y') }}</td>
                                    <td class="p-4 border-b text-sm text-red-600 font-bold">
                                        Rp {{ number_format($laporan->denda, 0, ',', '.') }}
                                    </td>
                                    <td class="p-4 border-b text-center">
                                        @if($laporan->denda > 0 && !$laporan->denda_dibayar)
                                            <button onclick="payDenda({{ $laporan->id }})" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-xs font-bold transition">
                                                <i class="fas fa-credit-card mr-1"></i> Bayar Sekarang
                                            </button>
                                        @elseif($laporan->denda_dibayar)
                                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Lunas</span>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-10 text-center text-gray-500 italic">Data laporan tidak ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-6">
                        {{ $laporans->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script Midtrans Snap --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    
    <script>
        function payDenda(id) {
            // Panggil rute generate snap token di LaporanController
            fetch(`/admin/laporan/${id}/generate-snap-token`)
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.token) {
                        // Jalankan pop-up Midtrans
                        window.snap.pay(data.token, {
                            onSuccess: function(result) {
                                alert("Pembayaran Berhasil!");
                                location.reload();
                            },
                            onPending: function(result) {
                                alert("Menunggu Pembayaran...");
                                location.reload();
                            },
                            onError: function(result) {
                                alert("Pembayaran Gagal!");
                            },
                            onClose: function() {
                                alert('Anda menutup popup tanpa menyelesaikan pembayaran.');
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(error.message || 'Terjadi kesalahan sistem saat menghubungi Midtrans.');
                });
        }
    </script>
</x-app-layout>