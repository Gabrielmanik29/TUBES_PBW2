<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Peminjaman') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Item Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Barang</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nama Barang</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $peminjaman->item->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Kategori</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $peminjaman->item->category->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Jumlah Dipinjam</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $peminjaman->quantity }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Borrowing Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Peminjaman</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tanggal Pinjam</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $peminjaman->tanggal_pinjam->format('d F Y') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tanggal Kembali</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $peminjaman->tanggal_kembali->format('d F Y') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($peminjaman->status == 'diajukan') bg-yellow-100 text-yellow-800
                                        @elseif($peminjaman->status == 'disetujui') bg-green-100 text-green-800
                                        @elseif($peminjaman->status == 'ditolak') bg-red-100 text-red-800
                                        @elseif($peminjaman->status == 'dibatalkan') bg-gray-100 text-gray-800
                                        @elseif($peminjaman->status == 'dikembalikan') bg-blue-100 text-blue-800
                                        @endif">
                                        {{ ucfirst($peminjaman->status) }}
                                    </span>
                                </div>
                                @if($peminjaman->tanggal_pengembalian_aktual)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tanggal Pengembalian Aktual</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $peminjaman->tanggal_pengembalian_aktual->format('d F Y') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($peminjaman->notes)
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Catatan</h3>
                        <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded">{{ $peminjaman->notes }}</p>
                    </div>
                    @endif

                    <!-- Fine Information -->
                    @if($peminjaman->status == 'dikembalikan' && $peminjaman->tanggal_pengembalian_aktual > $peminjaman->tanggal_kembali)
                    <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-red-800 mb-2">Informasi Denda</h3>
                        <div class="space-y-2">
                            <p class="text-sm text-red-700">
                                Terlambat: {{ $peminjaman->tanggal_pengembalian_aktual->diffInDays($peminjaman->tanggal_kembali) }} hari
                            </p>
                            <p class="text-sm text-red-700">
                                Denda: Rp {{ number_format($peminjaman->hitungDenda(), 0, ',', '.') }}
                            </p>
                            @if($peminjaman->denda_dibayar)
                            <p class="text-sm text-green-700">Status: Sudah dibayar</p>
                            @else
                            <p class="text-sm text-red-700">Status: Belum dibayar</p>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="mt-8 flex justify-between">
                        <a href="{{ route('peminjamans.history') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Kembali ke Riwayat
                        </a>

                        <div class="flex space-x-2">
                            @if($peminjaman->status == 'diajukan' && $peminjaman->user_id == Auth::id())
                            <button onclick="cancelPeminjaman('{{ $peminjaman->id }}')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Batalkan Peminjaman
                            </button>
                            @endif

                            @if($peminjaman->status == 'diajukan' && Auth::user()->isAdmin())
                            <button onclick="rejectPeminjaman('{{ $peminjaman->id }}')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Tolak Peminjaman
                            </button>
                            <button onclick="approvePeminjaman('{{ $peminjaman->id }}')" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Setujui Peminjaman
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function cancelPeminjaman(id) {
            if (confirm('Apakah Anda yakin ingin membatalkan peminjaman ini?')) {
                fetch(`/peminjamans/${id}/cancel`, {
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
                            window.location.href = '{{ route("peminjamans.history") }}';
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat membatalkan peminjaman');
                    });
            }
        }
    </script>
</x-app-layout>