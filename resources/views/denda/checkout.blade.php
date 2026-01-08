<x-app-layout>
    @push('styles')
        <style>
            .card-hover:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            }

            .loading-overlay {
                background: rgba(255, 255, 255, 0.9);
            }
        </style>
    @endpush

    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
            <i class="fas fa-credit-card text-indigo-600"></i>
            {{ __('Pembayaran Denda') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 rounded-r-xl p-4 mb-6 shadow-sm">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('warning'))
                <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-r-xl p-4 mb-6 shadow-sm">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">{{ session('warning') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Info Peminjaman -->
            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6 card-hover transition duration-300">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-file-contract text-indigo-500"></i>
                        Detail Peminjaman
                    </h3>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Info Barang -->
                        <div class="flex items-start space-x-4">
                            <div
                                class="h-16 w-16 rounded-xl bg-gray-100 border border-gray-200 flex items-center justify-center overflow-hidden flex-shrink-0">
                                @if($peminjaman->item->photo)
                                    <img src="{{ asset('storage/' . $peminjaman->item->photo) }}"
                                        class="h-full w-full object-cover">
                                @else
                                    <i class="fas fa-box text-gray-400 text-2xl"></i>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">{{ $peminjaman->item->name }}</h4>
                                <p class="text-sm text-gray-500">{{ $peminjaman->item->category->name ?? 'Umum' }}</p>
                                <p class="text-sm text-gray-500">Qty: {{ $peminjaman->quantity }}</p>
                            </div>
                        </div>

                        <!-- Info Jadwal -->
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Tanggal Pinjam</span>
                                <span
                                    class="text-sm font-medium text-gray-900">{{ $peminjaman->tanggal_pinjam->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Tanggal Kembali</span>
                                <span
                                    class="text-sm font-medium text-gray-900">{{ $peminjaman->tanggal_kembali->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Dikembalikan</span>
                                <span
                                    class="text-sm font-medium text-gray-900">{{ $peminjaman->tanggal_pengembalian_aktual->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rincian Denda -->
            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6 card-hover transition duration-300">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-calculator text-indigo-500"></i>
                        Rincian Denda
                    </h3>
                </div>

                <div class="p-6">
                    <div class="bg-indigo-50 rounded-xl p-4 mb-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-indigo-800">Total Denda yang Harus Dibayar</span>
                            <span class="text-2xl font-bold text-indigo-600">
                                Rp {{ number_format($peminjaman->denda, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-3 text-sm text-gray-600">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                            <p>Pembayaran denda keterlambatan pengembalian barang.</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <i class="fas fa-shield-alt text-green-500 mt-0.5"></i>
                            <p>Pembayaran diproses dengan aman melalui Midtrans.</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <i class="fas fa-clock text-yellow-500 mt-0.5"></i>
                            <p>Silakan selesaikan pembayaran dalam 24 jam.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Pembayaran -->
            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-hover transition duration-300">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-credit-card text-indigo-500"></i>
                        Metode Pembayaran
                    </h3>
                </div>

                <div class="p-6">
                    <!-- Loading Overlay -->
                    <div id="loading-overlay"
                        class="loading-overlay fixed inset-0 z-50 hidden flex items-center justify-center">
                        <div class="text-center">
                            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-indigo-600 mx-auto mb-4">
                            </div>
                            <p class="text-gray-700 font-medium">Memproses pembayaran...</p>
                            <p class="text-sm text-gray-500">Mohon tunggu sebentar</p>
                        </div>
                    </div>

                    <!-- Snap Token Container -->
                    <div id="snap-container" class="mb-4"></div>

                    <!-- Tombol Bayar -->
                    <button id="pay-button" onclick="startPayment()"
                        class="w-full py-4 px-6 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transition duration-300 flex items-center justify-center gap-2">
                        <i class="fas fa-lock"></i>
                        Bayar Sekarang Rp {{ number_format($peminjaman->denda, 0, ',', '.') }}
                    </button>

                    <!-- Info Tambahan -->
                    <div class="mt-4 text-center text-xs text-gray-500">
                        <p>Dengan mengklik tombol di atas, Anda menyetujui</p>
                        <p><a href="#" class="text-indigo-600 hover:underline">Syarat & Ketentuan</a> yang berlaku</p>
                    </div>
                </div>
            </div>

            <!-- Tombol Kembali -->
            <div class="mt-6 text-center">
                <a href="{{ route('my.borrowings') }}"
                    class="inline-flex items-center text-gray-600 hover:text-gray-900 transition">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Riwayat Peminjaman
                </a>
            </div>

        </div>
    </div>

    @push('scripts')
        <!-- Midtrans Snap JS -->
        <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

        <script>
            // Data dari server
            const peminjamanId = {{ $peminjaman->id }};
            const snapToken = '{{ $peminjaman->snap_token_denda }}';
            const checkoutUrl = '{{ route('denda.checkout') }}';

            // Fungsi untuk memulai pembayaran
            function startPayment() {
                // Show loading
                document.getElementById('loading-overlay').classList.remove('hidden');
                document.getElementById('pay-button').disabled = true;
                document.getElementById('pay-button').classList.add('opacity-50', 'cursor-not-allowed');

                // Jika sudah ada snap token, langsung tampilkan Midtrans
                if (snapToken) {
                    showMidtrans();
                } else {
                    // Request new snap token dari server
                    fetch(checkoutUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            peminjaman_id: peminjamanId
                        })
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                // Tampilkan Midtrans dengan token baru
                                window.snap.embed(data.snap_token, {
                                    embedId: 'snap-container',
                                    onSuccess: function (result) {
                                        handlePaymentSuccess(result);
                                    },
                                    onPending: function (result) {
                                        handlePaymentPending(result);
                                    },
                                    onError: function (result) {
                                        handlePaymentError(result);
                                    }
                                });
                            } else {
                                throw new Error(data.message || 'Gagal membuat transaksi');
                            }
                        })
                        .catch(error => {
                            console.error('Checkout Error:', error);
                            alert('Error: ' + error.message);
                            hideLoading();
                        });
                }
            }

            // Tampilkan Midtrans
            function showMidtrans() {
                window.snap.embed(snapToken, {
                    embedId: 'snap-container',
                    onSuccess: function (result) {
                        handlePaymentSuccess(result);
                    },
                    onPending: function (result) {
                        handlePaymentPending(result);
                    },
                    onError: function (result) {
                        handlePaymentError(result);
                    }
                });
                hideLoading();
            }

            // Handle payment success
            function handlePaymentSuccess(result) {
                console.log('Payment success:', result);
                document.getElementById('loading-overlay').classList.remove('hidden');

                // Redirect ke halaman finish
                window.location.href = '{{ route('denda.finish', ['peminjamanId' => $peminjaman->id]) }}?order_id=' + result.order_id;
            }

            // Handle payment pending
            function handlePaymentPending(result) {
                console.log('Payment pending:', result);
                alert('Pembayaran pending. Silakan selesaikan pembayaran.');
                hideLoading();
            }

            // Handle payment error
            function handlePaymentError(result) {
                console.log('Payment error:', result);
                alert('Pembayaran gagal: ' + (result.status_message || 'Silakan coba lagi.'));
                hideLoading();
            }

            // Hide loading overlay
            function hideLoading() {
                document.getElementById('loading-overlay').classList.add('hidden');
                document.getElementById('pay-button').disabled = false;
                document.getElementById('pay-button').classList.remove('opacity-50', 'cursor-not-allowed');
            }

            // Auto-hide flash messages
            setTimeout(function () {
                const flashMessages = document.querySelectorAll('.bg-green-100, .bg-red-100, .bg-yellow-100');
                flashMessages.forEach(function (message) {
                    message.style.transition = 'opacity 0.5s ease-out';
                    message.style.opacity = '0';
                    setTimeout(() => message.remove(), 500);
                });
            }, 5000);
        </script>
    @endpush
</x-app-layout>