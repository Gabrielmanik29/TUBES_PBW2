<!-- GANTI bagian script yang ada dengan ini: -->
@push('scripts')
<script>
function showDetailModal(peminjamanId) {
    console.log('Loading detail for peminjaman ID:', peminjamanId);
    
    // Show loading state
    document.getElementById('modalContent').innerHTML = `
        <div class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="mt-2 text-gray-600">Memuat data...</p>
        </div>
    `;
    
    document.getElementById('detailModal').classList.remove('hidden');
    
    // Fetch data dari API
    fetch(`/api/peminjaman/${peminjamanId}/detail`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Data received:', data);
            
            // Format tanggal untuk ditampilkan
            const formattedDenda = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(data.denda);
            
            // Build HTML content
            let htmlContent = `
                <div class="space-y-6">
                    <!-- Header -->
                    <div class="border-b pb-4">
                        <h4 class="text-lg font-bold text-gray-900">${data.item_name}</h4>
                        <p class="text-sm text-gray-600">${data.category}</p>
                    </div>
                    
                    <!-- Detail Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h5 class="font-medium text-gray-500 text-sm">Jumlah Dipinjam</h5>
                            <p class="text-gray-900">${data.quantity} unit</p>
                        </div>
                        <div>
                            <h5 class="font-medium text-gray-500 text-sm">Status</h5>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${data.status_class}">
                                ${data.status}
                            </span>
                        </div>
                        <div>
                            <h5 class="font-medium text-gray-500 text-sm">Tanggal Pinjam</h5>
                            <p class="text-gray-900">${data.tanggal_pinjam}</p>
                        </div>
                        <div>
                            <h5 class="font-medium text-gray-500 text-sm">Tanggal Kembali</h5>
                            <p class="text-gray-900">${data.tanggal_kembali}</p>
                        </div>
            `;
            
            // Tambahkan tanggal pengembalian jika ada
            if (data.tanggal_pengembalian) {
                htmlContent += `
                        <div>
                            <h5 class="font-medium text-gray-500 text-sm">Dikembalikan Pada</h5>
                            <p class="text-gray-900">${data.tanggal_pengembalian}</p>
                        </div>
                `;
            }
            
            // Tambahkan informasi denda jika ada
            if (data.denda > 0) {
                htmlContent += `
                        <div>
                            <h5 class="font-medium text-gray-500 text-sm">Denda</h5>
                            <p class="text-gray-900 font-semibold">${formattedDenda}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${data.denda_dibayar ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                                ${data.denda_dibayar ? '✓ Lunas' : '✗ Belum Dibayar'}
                            </span>
                        </div>
                `;
            }
            
            htmlContent += `
                    </div>
            `;
            
            // Tambahkan section keterlambatan jika ada
            if (data.keterlambatan > 0) {
                htmlContent += `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h5 class="text-sm font-medium text-red-800">Keterlambatan</h5>
                                <div class="mt-1 text-sm text-red-700">
                                    <p>Terlambat <span class="font-bold">${data.keterlambatan}</span> hari</p>
                                    <p class="text-xs mt-1">Denda dihitung Rp 10.000 per hari keterlambatan</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            htmlContent += `
                    <!-- Action Buttons -->
                    <div class="pt-4 border-t flex justify-end space-x-3">
                        <button onclick="closeDetailModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Tutup
                        </button>
                        ${data.denda > 0 && !data.denda_dibayar ? `
                        <form action="/payment/${peminjamanId}/pay" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700">
                                Bayar Denda
                            </button>
                        </form>
                        ` : ''}
                    </div>
                </div>
            `;
            
            document.getElementById('modalContent').innerHTML = htmlContent;
        })
        .catch(error => {
            console.error('Error fetching detail:', error);
            document.getElementById('modalContent').innerHTML = `
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Gagal memuat data</h3>
                    <p class="mt-1 text-sm text-gray-500">${error.message}</p>
                    <div class="mt-6">
                        <button onclick="closeDetailModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Tutup
                        </button>
                    </div>
                </div>
            `;
        });
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.add('hidden');
}

// Close modal when clicking outside or pressing ESC
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('detailModal');
    
    if (modal) {
        // Click outside to close
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeDetailModal();
            }
        });
        
        // ESC key to close
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeDetailModal();
            }
        });
    }
});

// Debug: Check if function is available
console.log('showDetailModal function loaded');
</script>
@endpush