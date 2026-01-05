<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Permintaan Peminjaman') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
<<<<<<< HEAD
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Permintaan Peminjaman Pending</h3>
                        <p class="mt-1 text-sm text-gray-600">Kelola permintaan peminjaman yang belum diproses</p>
                    </div>

                    @if($peminjamans->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Barang
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Peminjam
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Jumlah
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal Pinjam
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal Kembali
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Catatan
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($peminjamans as $peminjaman)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $peminjaman->item->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $peminjaman->item->category->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $peminjaman->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $peminjaman->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $peminjaman->quantity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $peminjaman->tanggal_pinjam->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $peminjaman->tanggal_kembali->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                                        {{ $peminjaman->notes ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('peminjamans.show', $peminjaman) }}"
                                                class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md text-sm font-medium">
                                                Lihat Detail
                                            </a>
                                            <button onclick="rejectPeminjaman('{{ $peminjaman->id }}')"
                                                class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md text-sm font-medium">
                                                Tolak
                                            </button>
                                            <button onclick="approvePeminjaman('{{ $peminjaman->id }}')"
                                                class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1 rounded-md text-sm font-medium">
                                                Setujui
                                            </button>
                                        </div>
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
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada permintaan pending</h3>
                        <p class="mt-1 text-sm text-gray-500">Semua permintaan peminjaman telah diproses.</p>
                    </div>
=======
                    <!-- Filter dan Pencarian -->
                    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Permintaan Peminjaman</h3>
                            <p class="mt-1 text-sm text-gray-600">Kelola permintaan peminjaman yang belum diproses</p>
                        </div>
                        <form method="GET" class="flex items-center gap-2">
                            <select name="status" onchange="this.form.submit()"
                                class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>Semua Status
                                </option>
                                <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Diajukan
                                </option>
                                <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>
                                    Disetujui</option>
                                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak
                                </option>
                                <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>
                                    Dikembalikan</option>
                            </select>
                            <input type="text" name="search" placeholder="Cari user atau barang..."
                                value="{{ request('search') }}"
                                class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <button type="submit"
                                class="bg-blue-500 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-600">
                                Cari
                            </button>
                        </form>
                    </div>

                    @if($peminjamans->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Barang
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Peminjam
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Jumlah
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal Pinjam
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal Kembali
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Denda
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($peminjamans as $peminjaman)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $peminjaman->item->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $peminjaman->item->category->name ?? 'Tanpa Kategori' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $peminjaman->user->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $peminjaman->user->email }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $peminjaman->quantity }}
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
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$peminjaman->status] }}">
                                                    {{ $statusLabels[$peminjaman->status] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($peminjaman->denda > 0)
                                                    @if($peminjaman->denda_dibayar)
                                                        <span class="text-green-600 font-semibold">Rp
                                                            {{ number_format($peminjaman->denda, 0, ',', '.') }} (Lunas)</span>
                                                    @else
                                                        <span class="text-red-600 font-semibold">Rp
                                                            {{ number_format($peminjaman->denda, 0, ',', '.') }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('admin.peminjaman.show', $peminjaman) }}"
                                                        class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md text-sm font-medium">
                                                        Detail
                                                    </a>
                                                    @if($peminjaman->status == 'diajukan')
                                                        <button onclick="rejectPeminjaman('{{ $peminjaman->id }}')"
                                                            class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md text-sm font-medium">
                                                            Tolak
                                                        </button>
                                                        <button onclick="approvePeminjaman('{{ $peminjaman->id }}')"
                                                            class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1 rounded-md text-sm font-medium">
                                                            Setujui
                                                        </button>
                                                    @endif
                                                    @if($peminjaman->status == 'disetujui')
                                                        <button
                                                            onclick="showReturnModal('{{ $peminjaman->id }}', '{{ $peminjaman->tanggal_kembali->format('Y-m-d') }}')"
                                                            class="text-purple-600 hover:text-purple-900 bg-purple-50 hover:bg-purple-100 px-3 py-1 rounded-md text-sm font-medium">
                                                            Kembalikan
                                                        </button>
                                                    @endif
                                                    @if($peminjaman->denda > 0 && !$peminjaman->denda_dibayar)
                                                        <button onclick="confirmDendaPaid('{{ $peminjaman->id }}')"
                                                            class="text-orange-600 hover:text-orange-900 bg-orange-50 hover:bg-orange-100 px-3 py-1 rounded-md text-sm font-medium">
                                                            Lunaskan
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $peminjamans->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data</h3>
                            <p class="mt-1 text-sm text-gray-500">Tidak ada peminjaman yang sesuai dengan filter.</p>
                        </div>
>>>>>>> d55b9af1f343e0e3324d653f7222d76df8c70cd2
                    @endif
                </div>
            </div>
        </div>
    </div>

<<<<<<< HEAD
    <script>
        function approvePeminjaman(id) {
            if (confirm('Apakah Anda yakin ingin menyetujui peminjaman ini?')) {
                fetch(`/admin/peminjamans/${id}/approve`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
=======
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
>>>>>>> d55b9af1f343e0e3324d653f7222d76df8c70cd2
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
<<<<<<< HEAD
                            alert(data.message);
=======
                            alert(data.message || 'Gagal menyetujui peminjaman');
>>>>>>> d55b9af1f343e0e3324d653f7222d76df8c70cd2
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menyetujui peminjaman');
                    });
            }
        }

        function rejectPeminjaman(id) {
<<<<<<< HEAD
            if (confirm('Apakah Anda yakin ingin menolak peminjaman ini?')) {
                fetch(`/admin/peminjamans/${id}/reject`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
=======
            document.getElementById('rejectForm').action = `/admin/peminjaman/${id}/reject`;
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

            // Hitung denda awal
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

            // Show loading
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

                        // Simpan ke input hidden
                        document.getElementById('return_date_input').value = tanggalPengembalian;

                        // Update tombol text jika ada denda
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

        // Hitung denda saat tanggal berubah
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
>>>>>>> d55b9af1f343e0e3324d653f7222d76df8c70cd2
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
<<<<<<< HEAD
                            alert(data.message);
=======
                            alert(data.message || 'Gagal mengkonfirmasi pembayaran');
>>>>>>> d55b9af1f343e0e3324d653f7222d76df8c70cd2
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
<<<<<<< HEAD
                        alert('Terjadi kesalahan saat menolak peminjaman');
                    });
            }
        }
=======
                        alert('Terjadi kesalahan');
                    });
            }
        }

        // Close modal when clicking outside
        document.getElementById('returnModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeReturnModal();
            }
        });
>>>>>>> d55b9af1f343e0e3324d653f7222d76df8c70cd2
    </script>
</x-app-layout>