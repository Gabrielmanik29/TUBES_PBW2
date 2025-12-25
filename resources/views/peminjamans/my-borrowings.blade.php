<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Peminjaman Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($peminjamans->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pinjam</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Kembali</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($peminjamans as $peminjaman)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $peminjaman->item->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $peminjaman->item->category->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $peminjaman->quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $peminjaman->tanggal_pinjam->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $peminjaman->tanggal_kembali->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    @if($peminjaman->status == 'diajukan') bg-yellow-100 text-yellow-800
                                                    @elseif($peminjaman->status == 'disetujui') bg-green-100 text-green-800
                                                    @elseif($peminjaman->status == 'ditolak') bg-red-100 text-red-800
                                                    @elseif($peminjaman->status == 'dibatalkan') bg-gray-100 text-gray-800
                                                    @elseif($peminjaman->status == 'dikembalikan') bg-blue-100 text-blue-800
                                                    @endif">
                                            {{ ucfirst($peminjaman->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('peminjamans.show', $peminjaman) }}" class="text-indigo-600 hover:text-indigo-900">Lihat Detail</a>
                                        @if($peminjaman->status == 'diajukan')
                                        <button onclick="cancelPeminjaman('{{ $peminjaman->id }}')" class="ml-2 text-red-600 hover:text-red-900">Batalkan</button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $peminjamans->links() }}
                    </div>
                    @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">Belum ada peminjaman aktif.</p>
                    </div>
                    @endif
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
                            location.reload();
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