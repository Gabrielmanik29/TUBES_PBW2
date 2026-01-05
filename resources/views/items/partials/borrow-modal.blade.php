<!-- Borrow Modal -->
<div id="borrowModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden overflow-y-auto" role="dialog"
    aria-modal="true" aria-labelledby="borrowModalTitle">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0"
            id="borrowModalContent">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-6 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-handshake text-xl"></i>
                        </div>
                        <div>
                            <h2 id="borrowModalTitle" class="text-2xl font-bold">Ajukan Peminjaman</h2>
                            <p class="text-blue-100 text-sm">Isi form di bawah untuk meminjam barang</p>
                        </div>
                    </div>
                    <button onclick="closeBorrowModal()"
                        class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition">
                        <i class="fas fa-times text-white"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6">

                <form id="borrowForm">
                    @csrf

                    <!-- Hidden Item ID -->
                    <input type="hidden" id="modalItemId" name="item_id">

                    <!-- Item Information Card -->
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-6 border border-blue-100">
                        <div class="flex items-start gap-4">
                            <div
                                class="w-16 h-16 bg-gradient-to-r from-blue-100 to-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-box text-2xl text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <h3 id="modalItemName" class="text-xl font-bold text-gray-800 mb-1">Nama Barang</h3>
                                <div class="flex items-center gap-4 text-sm text-gray-600">
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-tag text-blue-500"></i>
                                        <span id="modalCategoryName">Kategori</span>
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-cubes text-green-500"></i>
                                        <span id="modalMaxStock">0</span> tersedia
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quantity Selection -->
                    <div>
                        <label for="quantity" class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-list-ol text-blue-500 mr-2"></i>Jumlah yang ingin dipinjam
                        </label>
                        <div class="flex items-center gap-4">
                            <div class="flex-1 relative">
                                <input type="number" id="quantity" name="quantity" min="1" max="1" value="1" required
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition text-center text-lg font-semibold">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-4">
                                    <button type="button" onclick="decreaseQuantity()"
                                        class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition">
                                        <i class="fas fa-minus text-gray-600 text-sm"></i>
                                    </button>
                                </div>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                    <button type="button" onclick="increaseQuantity()"
                                        class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition">
                                        <i class="fas fa-plus text-gray-600 text-sm"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="text-sm text-gray-500 whitespace-nowrap">
                                Maksimal: <span id="quantityMax" class="font-semibold">1</span>
                            </div>
                        </div>
                        <div id="quantityWarning"
                            class="hidden mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                                <span class="text-sm text-yellow-800 font-medium">Pastikan jumlah yang dipilih sesuai
                                    kebutuhan</span>
                            </div>
                        </div>
                    </div>

                    <!-- Date Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tanggal Pinjam -->
                        <div>
                            <label for="tanggal_pinjam" class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-calendar-plus text-blue-500 mr-2"></i>Tanggal Meminjam
                            </label>
                            <div class="relative">
                                <input type="date" id="tanggal_pinjam" name="tanggal_pinjam" required
                                    min="{{ date('Y-m-d') }}"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                    <i class="fas fa-calendar text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Tanggal Kembali -->
                        <div>
                            <label for="tanggal_kembali" class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-calendar-check text-green-500 mr-2"></i>Tanggal Pengembalian
                            </label>
                            <div class="relative">
                                <input type="date" id="tanggal_kembali" name="tanggal_kembali" required
                                    min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                    <i class="fas fa-calendar text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Duration Info -->
                    <div id="durationInfo" class="hidden bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-blue-800">Durasi Peminjaman</h4>
                                <p id="durationText" class="text-blue-600 text-sm">0 hari</p>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-sticky-note text-purple-500 mr-2"></i>Keterangan (Opsional)
                        </label>
                        <textarea id="notes" name="notes" rows="3"
                            placeholder="Tambahkan alasan atau informasi tambahan untuk peminjaman..."
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition resize-none"></textarea>
                        <p class="text-xs text-gray-500 mt-2">Contoh: Untuk keperluan tugas laboratorium, presentasi,
                            atau acara organisasi</p>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" id="terms" name="terms" required
                                class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <div class="text-sm text-gray-700">
                                <label for="terms" class="font-medium cursor-pointer">
                                    Saya menyetujui syarat dan ketentuan peminjaman
                                </label>
                                <ul class="mt-2 text-xs text-gray-600 space-y-1">
                                    <li>• Mengembalikan barang sesuai tanggal yang telah ditentukan</li>
                                    <li>• Bertanggung jawab penuh atas kerusakan atau kehilangan</li>
                                    <li>• Tidak boleh meminjamkan barang kepada pihak lain</li>
                                    <li>• Akan dikenakan denda jika terlambat mengembalikan</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 rounded-b-2xl">
                <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                    <button type="button" onclick="closeBorrowModal()"
                        class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-xl font-medium transition">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="submit" form="borrowForm"
                        class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-medium transition shadow-lg hover:shadow-xl"
                        id="submitButton">
                        <i class="fas fa-paper-plane mr-2"></i>Ajukan Peminjaman
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay for Modal -->
<div id="modalLoading" class="hidden fixed inset-0 bg-white bg-opacity-90 z-[60] flex items-center justify-center">
    <div class="text-center">
        <div
            class="w-16 h-16 border-4 border-t-blue-600 border-r-transparent border-b-purple-600 border-l-transparent rounded-full animate-spin mx-auto mb-4">
        </div>
        <p class="text-gray-600 font-medium">Memproses permintaan...</p>
    </div>
</div>

<script>
    // Modal JavaScript Functions
    function openBorrowModal(itemId, itemName, maxStock, categoryName) {
        console.log('Opening borrow modal for:', itemId, itemName, maxStock, categoryName);

        // Set item information
        document.getElementById('modalItemId').value = itemId;
        document.getElementById('modalItemName').textContent = itemName;
        document.getElementById('modalCategoryName').textContent = categoryName;
        document.getElementById('modalMaxStock').textContent = maxStock;
        document.getElementById('quantityMax').textContent = maxStock;

        // Set quantity limits
        const quantityInput = document.getElementById('quantity');
        quantityInput.max = maxStock;
        quantityInput.value = 1;

        // Set default dates
        const today = new Date().toISOString().split('T')[0];
        const tomorrow = new Date(Date.now() + 86400000).toISOString().split('T')[0];

        document.getElementById('tanggal_pinjam').value = today;
        document.getElementById('tanggal_kembali').value = tomorrow;

        // Show modal with animation
        const modal = document.getElementById('borrowModal');
        const modalContent = document.getElementById('borrowModalContent');

        modal.classList.remove('hidden');

        // Trigger animation
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);

        // Disable body scroll
        document.body.style.overflow = 'hidden';

        // Focus on quantity input
        setTimeout(() => {
            document.getElementById('quantity').focus();
        }, 300);
    }

    function closeBorrowModal() {
<<<<<<< HEAD
        const modal = document.getElementById('borrowModal');
        const modalContent = document.getElementById('borrowModalContent');

=======
        // Hide loading first
        hideLoading();

        const modal = document.getElementById('borrowModal');
        const modalContent = document.getElementById('borrowModalContent');

        if (!modal || !modalContent) {
            console.error('Modal elements not found');
            return;
        }

>>>>>>> d55b9af1f343e0e3324d653f7222d76df8c70cd2
        // Hide modal with animation
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';

<<<<<<< HEAD
            // Reset form
            document.getElementById('borrowForm').reset();
            hideLoading();
=======
            // Reset form after modal is hidden
            const form = document.getElementById('borrowForm');
            if (form) {
                form.reset();
            }

            // Reset modal content
            document.getElementById('modalItemId').value = '';
            document.getElementById('modalItemName').textContent = 'Nama Barang';
            document.getElementById('modalCategoryName').textContent = 'Kategori';
            document.getElementById('modalMaxStock').textContent = '0';
            document.getElementById('quantityMax').textContent = '1';
            document.getElementById('quantity').max = 1;
            document.getElementById('quantity').value = 1;

            // Reset duration info
            document.getElementById('durationInfo').classList.add('hidden');
>>>>>>> d55b9af1f343e0e3324d653f7222d76df8c70cd2
        }, 300);
    }

    // Quantity management
    function increaseQuantity() {
        const quantityInput = document.getElementById('quantity');
        const max = parseInt(quantityInput.max);
        const current = parseInt(quantityInput.value);

        if (current < max) {
            quantityInput.value = current + 1;
            updateQuantityWarning();
        }
    }

    function decreaseQuantity() {
        const quantityInput = document.getElementById('quantity');
        const min = parseInt(quantityInput.min);
        const current = parseInt(quantityInput.value);

        if (current > min) {
            quantityInput.value = current - 1;
            updateQuantityWarning();
        }
    }

    function updateQuantityWarning() {
        const quantityInput = document.getElementById('quantity');
        const warning = document.getElementById('quantityWarning');
        const quantity = parseInt(quantityInput.value);
        const max = parseInt(quantityInput.max);

        if (quantity >= max) {
            warning.classList.remove('hidden');
        } else {
            warning.classList.add('hidden');
        }
    }

    // Date validation and duration calculation
    function calculateDuration() {
        const pinjamDate = new Date(document.getElementById('tanggal_pinjam').value);
        const kembaliDate = new Date(document.getElementById('tanggal_kembali').value);
        const durationInfo = document.getElementById('durationInfo');
        const durationText = document.getElementById('durationText');

        if (pinjamDate && kembaliDate && kembaliDate > pinjamDate) {
            const diffTime = Math.abs(kembaliDate - pinjamDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            durationText.textContent = `${diffDays} hari`;
            durationInfo.classList.remove('hidden');

            // Set minimum date for return date
            document.getElementById('tanggal_kembali').min = document.getElementById('tanggal_pinjam').value;

            // Update return date if it's before minimum
            if (new Date(document.getElementById('tanggal_kembali').value) <= new Date(document.getElementById('tanggal_pinjam').value)) {
                const minReturnDate = new Date(pinjamDate.getTime() + 86400000).toISOString().split('T')[0];
                document.getElementById('tanggal_kembali').value = minReturnDate;
            }
        } else {
            durationInfo.classList.add('hidden');
        }
    }

    // Form validation and submission
    function validateBorrowForm() {
        const form = document.getElementById('borrowForm');
        const quantity = parseInt(document.getElementById('quantity').value);
        const maxStock = parseInt(document.getElementById('quantity').max);
        const pinjamDate = document.getElementById('tanggal_pinjam').value;
        const kembaliDate = document.getElementById('tanggal_kembali').value;
        const terms = document.getElementById('terms').checked;

        if (quantity < 1 || quantity > maxStock) {
            alert('Jumlah yang dipilih tidak valid');
            return false;
        }

        if (!pinjamDate || !kembaliDate) {
            alert('Harap pilih tanggal peminjaman dan pengembalian');
            return false;
        }

        if (new Date(kembaliDate) <= new Date(pinjamDate)) {
            alert('Tanggal pengembalian harus lebih dari tanggal peminjaman');
            return false;
        }

        if (!terms) {
            alert('Harap setujui syarat dan ketentuan');
            return false;
        }

        return true;
    }

    function showLoading() {
        document.getElementById('modalLoading').classList.remove('hidden');
        document.getElementById('submitButton').disabled = true;
        document.getElementById('submitButton').innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
    }

    function hideLoading() {
        document.getElementById('modalLoading').classList.add('hidden');
        document.getElementById('submitButton').disabled = false;
        document.getElementById('submitButton').innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Ajukan Peminjaman';
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function () {
        // Quantity input change
        document.getElementById('quantity').addEventListener('input', updateQuantityWarning);

        // Date changes
        document.getElementById('tanggal_pinjam').addEventListener('change', calculateDuration);
        document.getElementById('tanggal_kembali').addEventListener('change', calculateDuration);


        // Form submission
        document.getElementById('borrowForm').addEventListener('submit', function (e) {
            e.preventDefault();

            if (validateBorrowForm()) {
                showLoading();

<<<<<<< HEAD
=======
                // Check if user is authenticated - using multiple methods
                const isAuthenticated =
                    document.querySelector('meta[name="user-authenticated"]') !== null ||
                    (window.Laravel && window.Laravel.isAuthenticated === true) ||
                    (window.Laravel && window.Laravel.userId !== undefined && window.Laravel.userId !== null);

                if (!isAuthenticated) {
                    hideLoading();
                    alert('Sesi Anda telah berakhir. Silakan login kembali.');
                    window.location.href = '/login';
                    return;
                }

>>>>>>> d55b9af1f343e0e3324d653f7222d76df8c70cd2
                // Get form data
                const formData = new FormData(this);
                const itemId = document.getElementById('modalItemId').value;
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // Submit via AJAX
<<<<<<< HEAD
=======
                console.log('Submitting borrow request for item:', itemId);
                console.log('CSRF Token:', csrfToken);

>>>>>>> d55b9af1f343e0e3324d653f7222d76df8c70cd2
                fetch(`/borrow/${itemId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
<<<<<<< HEAD
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        quantity: formData.get('quantity'),
                        tanggal_pinjam: formData.get('tanggal_pinjam'),
                        tanggal_kembali: formData.get('tanggal_kembali'),
                        notes: formData.get('notes')
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        hideLoading();

                        if (data.success) {
                            alert(data.message);
=======
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: formData
                })
                    .then(response => {
                        console.log('Response status:', response.status);
                        console.log('Response ok:', response.ok);

                        if (!response.ok) {
                            return response.text().then(text => {
                                console.error('Response text:', text);
                                throw new Error(`HTTP error! status: ${response.status}, message: ${text}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data);
                        hideLoading();

                        if (data.success) {
                            alert(data.message || 'Peminjaman berhasil diajukan!');
>>>>>>> d55b9af1f343e0e3324d653f7222d76df8c70cd2
                            closeBorrowModal();

                            // Refresh the page to update stock availability
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            alert(data.message || 'Terjadi kesalahan saat mengirim permintaan.');
                        }
                    })
                    .catch(error => {
<<<<<<< HEAD
                        hideLoading();
                        console.error('Error:', error);
                        alert('Terjadi kesalahan. Silakan coba lagi.');
=======
                        console.error('Fetch error:', error);
                        hideLoading();

                        // Show user-friendly error message
                        let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                        if (error.message.includes('HTTP error!')) {
                            errorMessage = 'Terjadi kesalahan server. Silakan coba lagi nanti.';
                        } else if (error.message.includes('Failed to fetch')) {
                            errorMessage = 'Koneksi internet bermasalah. Periksa koneksi Anda.';
                        }

                        alert(errorMessage);
>>>>>>> d55b9af1f343e0e3324d653f7222d76df8c70cd2
                    });
            }
        });

        // Initialize quantity warning
        updateQuantityWarning();

        // Initialize duration
        calculateDuration();
    });

    // Close modal when clicking outside
    document.getElementById('borrowModal').addEventListener('click', function (e) {
        if (e.target === this) {
            closeBorrowModal();
        }
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('borrowModal');
            if (!modal.classList.contains('hidden')) {
                closeBorrowModal();
            }
        }
    });
</script>