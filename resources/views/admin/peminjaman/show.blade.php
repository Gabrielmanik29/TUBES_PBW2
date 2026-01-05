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
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">Detail Peminjaman</h3>
                            <p class="text-sm text-gray-500 mt-1">ID: #{{ $peminjaman->id }}</p>
                        </div>
                        <a href="{{ route('admin.peminjaman') }}" class="text-blue-600 hover:text-blue-800">
                            &larr; Kembali ke Daftar
                        </a>
                    </div>

                    <!-- Info Peminjam & Barang -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Info Peminjam -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-700 mb-3">Informasi Peminjam</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Nama:</span>
                                    <span class="font-medium">{{ $peminjaman->user->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Email:</span>
                                    <span class="font-medium">{{ $peminjaman->user->email }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">No. HP:</span>
                                    <span class="font-medium">{{ $peminjaman->user->phone ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Info Barang -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-700 mb-3">Informasi Barang</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Nama Barang:</span>
                                    <span class="font-medium">{{ $peminjaman->item->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Kategori:</span>
                                    <span
                                        class="font-medium">{{ $peminjaman->item->category->name ?? 'Tanpa Kategori' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Jumlah:</span>
                                    <span class="font-medium">{{ $peminjaman->quantity }} unit</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Tanggal -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h4 class="font-semibold text-gray-700 mb-3">Informasi Tanggal</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500 block">Tanggal Pinjam</span>
                                <span class="font-semibold">{{ $peminjaman->tanggal_pinjam->format('d/m/Y') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 block">Tanggal Kembali</span>
                                <span class="font-semibold">{{ $peminjaman->tanggal_kembali->format('d/m/Y') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 block">Tanggal Pengembalian</span>
                                <span class="font-semibold">
                                    {{ $peminjaman->tanggal_pengembalian_aktual
    ? $peminjaman->tanggal_pengembalian_aktual->format('d/m/Y')
    : '-' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-500 block">Status</span>
                                @php
                                    $statusColors = [
                                        'diajukan' => 'bg-yellow-100 text-yellow-800',
                                        'disetujui' => 'bg-green-100 text-green-800',
                                        'ditolak' => 'bg-red-100 text-red-800',
                                        'dikembalikan' => 'bg-blue-100 text-blue-800',
                                        'dibatalkan' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $statusLabels = [
                                        'diajukan' => 'Diajukan',
                                        'disetujui' => 'Disetujui',
                                        'ditolak' => 'Ditolak',
                                        'dikembalikan' => 'Dikembalikan',
                                        'dibatalkan' => 'Dibatalkan',
                                    ];
                                @endphp
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$peminjaman->status] }}">
                                    {{ $statusLabels[$peminjaman->status] }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Info Denda -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h4 class="font-semibold text-gray-700 mb-3">Informasi Denda</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500 block">Denda Per Hari</span>
                                <span class="font-semibold text-lg">Rp 5.000</span>
                            </div>
                            @if($peminjaman->status == 'dikembalikan' && $peminjaman->tanggal_pengembalian_aktual)
                                @php
                                    $hariTerlambat = $peminjaman->hitungKeterlambatan();
                                    $isTerlambat = $peminjaman->isTerlambat();
                                @endphp
                                <div>
                                    <span class="text-gray-500 block">Keterlambatan</span>
                                    <span class="font-semibold {{ $isTerlambat ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $hariTerlambat }} hari
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-500 block">Total Denda</span>
                                    <span
                                        class="font-semibold text-lg {{ $peminjaman->denda > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        Rp {{ number_format($peminjaman->denda, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-500 block">Status Denda</span>
                                    @if($peminjaman->denda_dibayar)
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Lunas</span>
                                    @elseif($peminjaman->denda > 0)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Belum
                                            Bayar</span>
                                    @else
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Tidak
                                            Ada</span>
                                    @endif
                                </div>
                            @else
                                <div>
                                    <span class="text-gray-500 block">Total Denda</span>
                                    <span class="font-semibold text-gray-400">-</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 block">Status Denda</span>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ $peminjaman->status == 'disetujui' ? 'Belum Dikembalikan' : 'N/A' }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Catatan Penolakan -->
                    @if($peminjaman->status == 'ditolak' && $peminjaman->rejection_reason)
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                            <h4 class="font-semibold text-red-800 mb-2">Alasan Penolakan</h4>
                            <p class="text-red-700">{{ $peminjaman->rejection_reason }}</p>
                        </div>
                    @endif

                    <!-- Aksi -->
                    <div class="border-t pt-6">
                        <h4 class="font-semibold text-gray-700 mb-4">Aksi</h4>
                        <div class="flex flex-wrap gap-3">
                            @if($peminjaman->status == 'diajukan')
                                <button onclick="approvePeminjaman('{{ $peminjaman->id }}')"
                                    class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700">
                                    Setujui Peminjaman
                                </button>
                                <button onclick="showRejectModal()"
                                    class="bg-red-600 text-white px-4 py-2 rounded-md text-sm hover:bg-red-700">
                                    Tolak Peminjaman
                                </button>
                            @endif

                            @if($peminjaman->status == 'disetujui')
                                <button
                                    onclick="showReturnModal('{{ $peminjaman->id }}', '{{ $peminjaman->tanggal_kembali->format('Y-m-d') }}')"
                                    class="bg-purple-600 text-white px-4 py-2 rounded-md text-sm hover:bg-purple-700">
                                    Proses Pengembalian
                                </button>
                            @endif

                            @if($peminjaman->denda > 0 && !$peminjaman->denda_dibayar)
                                <button onclick="confirmDendaPaid('{{ $peminjaman->id }}')"
                                    class="bg-orange-600 text-white px-4 py-2 rounded-md text-sm hover:bg-orange-700">
                                    Konfirmasi Denda Lunas
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Reject -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tolak Peminjaman</h3>
                <form id="rejectForm" method="POST">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="rejection_reason" id="rejection_reason">
                    <div class="mb-4">
                        <label for="reject_notes" class="block text-sm font-medium text-gray-700 mb-2">Alasan
                            Penolakan</label>
                        <textarea id="reject_notes" rows="3"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500"
                            placeholder="Masukkan alasan penolakan..."></textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="closeRejectModal()"
                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm hover:bg-gray-400">
                            Batal
                        </button>
                        <button type="submit"
                            class="bg-red-600 text-white px-4 py-2 rounded-md text-sm hover:bg-red-700">
                            Tolak Peminjaman
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Return dengan Denda -->
    <div id="returnModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-[500px] shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Proses Pengembalian Barang</h3>
                <p class="text-sm text-gray-500 mb-4">Denda keterlambatan: <strong>Rp 5.000/hari</strong></p>

                <form id="returnForm" method="POST">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="tanggal_pengembalian" id="return_date_input">

                    <!-- Info Peminjaman -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Tanggal Kembali:</span>
                                <p class="font-semibold" id="tanggal_kembali_display">-</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Tanggal Pengembalian:</span>
                                <input type="date" id="tanggal_pengembalian_input" name="tanggal_pengembalian"
                                    value="{{ date('Y-m-d') }}"
                                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                        </div>
                    </div>

                    <!-- Info Denda -->
                    <div id="dendaInfo" class="hidden bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                            <span class="font-semibold text-yellow-800">Perhitungan Denda</span>
                        </div>
                        <div class="text-sm text-yellow-700">
                            <p>Keterlambatan: <strong id="hari_terlambat">0</strong> hari</p>
                            <p>Denda per hari: <strong>Rp 5.000</strong></p>
                            <p class="text-lg font-bold mt-1">Total Denda: <span id="total_denda"
                                    class="text-red-600">Rp 0</span></p>
                        </div>
                    </div>

                    <!-- Loading -->
                    <div id="dendaLoading" class="hidden text-center py-4">
                        <svg class="animate-spin h-5 w-5 text-purple-600 mx-auto" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <p class="text-sm text-gray-500 mt-2">Menghitung denda...</p>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeReturnModal()"
                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm hover:bg-gray-400">
                            Batal
                        </button>
                        <button type="submit" id="returnSubmitBtn"
                            class="bg-purple-600 text-white px-4 py-2 rounded-md text-sm hover:bg-purple-700">
                            Konfirmasi Pengembalian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentPeminjamanId = null;

        function approvePeminjaman(id) {
            if (confirm('Apakah Anda yakin ingin menyetujui peminjaman ini?')) {
                fetch(`/admin/peminjaman/${id}/approve`, {
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
                            alert(data.message || 'Gagal menyetujui peminjaman');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menyetujui peminjaman');
                    });
            }
        }

        function showRejectModal() {
            document.getElementById('rejectForm').action = `/admin/peminjaman/{{ $peminjaman->id }}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.getElementById('reject_notes').value = '';
        }

        document.getElementById('rejectForm').addEventListener('submit', function (e) {
            const reason = document.getElementById('reject_notes').value;
            if (!reason.trim()) {
                e.preventDefault();
                alert('Alasan penolakan wajib diisi');
                document.getElementById('reject_notes').focus();
            } else {
                document.getElementById('rejection_reason').value = reason;
            }
        });

        function showReturnModal(id, tanggalKembali) {
            currentPeminjamanId = id;
            document.getElementById('tanggal_kembali_display').textContent = formatDate(tanggalKembali);
            document.getElementById('returnForm').action = `/admin/peminjaman/${id}/return`;
            document.getElementById('returnModal').classList.remove('hidden');
            calculateDenda();
        }

        function closeReturnModal() {
            document.getElementById('returnModal').classList.add('hidden');
            currentPeminjamanId = null;
        }

        function formatDate(dateString) {
            const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        }

        function calculateDenda() {
            if (!currentPeminjamanId) return;

            const tanggalPengembalian = document.getElementById('tanggal_pengembalian_input').value;
            if (!tanggalPengembalian) return;

            document.getElementById('dendaLoading').classList.remove('hidden');
            document.getElementById('dendaInfo').classList.add('hidden');

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
                    document.getElementById('dendaLoading').classList.add('hidden');

                    if (data.success) {
                        document.getElementById('dendaInfo').classList.remove('hidden');
                        document.getElementById('hari_terlambat').textContent = data.hari_terlambat;
                        document.getElementById('total_denda').textContent = data.formatted_denda;
                        document.getElementById('return_date_input').value = tanggalPengembalian;

                        const submitBtn = document.getElementById('returnSubmitBtn');
                        if (data.denda > 0) {
                            submitBtn.textContent = `Konfirmasi Pengembalian (Denda: ${data.formatted_denda})`;
                        } else {
                            submitBtn.textContent = 'Konfirmasi Pengembalian';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('dendaLoading').classList.add('hidden');
                });
        }

        document.getElementById('tanggal_pengembalian_input').addEventListener('change', calculateDenda);

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
                            alert(data.message || 'Gagal mengkonfirmasi pembayaran');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan');
                    });
            }
        }

        document.getElementById('returnModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeReturnModal();
            }
        });
    </script>
</x-app-layout>