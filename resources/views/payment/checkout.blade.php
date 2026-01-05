@extends('layouts.app')

@section('title', 'Pembayaran - ' . config('app.name'))

@section('content')
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- Card Utama --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    {{-- Header --}}
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Pembayaran Pesanan</h2>
                            <p class="text-sm text-gray-500 mt-1">Order ID: {{ $order->order_id }}</p>
                        </div>
                        <a href="{{ route('orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-800">
                            &larr; Kembali
                        </a>
                    </div>

                    {{-- Alert Messages --}}
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Ringkasan Pesanan --}}
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h3 class="font-semibold text-gray-800 mb-3">Ringkasan Pesanan</h3>
                        <div class="space-y-2">
                            @foreach($order->orderItems as $item)
                                <div class="flex justify-between text-sm">
                                    <span>{{ $item->product->name ?? 'Product' }} x {{ $item->quantity }}</span>
                                    <span>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                            <hr class="my-2">
                            <div class="flex justify-between font-bold">
                                <span>Total Pembayaran</span>
                                <span class="text-xl text-green-600">Rp
                                    {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Form Pembayaran --}}
                    <div class="bg-blue-50 rounded-lg p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Metode Pembayaran</h3>

                        {{-- Informasi Customer --}}
                        <form id="payment-form">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->order_id }}">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" name="email" id="email" value="{{ Auth::user()->email }}" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                        No. HP <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="phone" id="phone" placeholder="081234567890" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                                </div>
                            </div>

                            {{-- Tombol Bayar Sekarang --}}
                            <button type="submit" id="pay-button"
                                class="w-full inline-flex justify-center items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-base text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                    </path>
                                </svg>
                                <span id="pay-button-text">Bayar Sekarang</span>
                            </button>
                        </form>

                        <p class="mt-3 text-sm text-gray-500 text-center">
                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                            Pembayaran aman dengan Midtrans
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Midtrans Snap.js Library - Client Key dari config --}}
    <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const payButton = document.getElementById('pay-button');
            const payButtonText = document.getElementById('pay-button-text');
            const paymentForm = document.getElementById('payment-form');

            // Event listener untuk form submission
            paymentForm.addEventListener('submit', function (e) {
                e.preventDefault();

                // Disable button saat proses
                payButton.disabled = true;
                payButtonText.textContent = 'Memproses...';

                // Ambil data form
                const formData = new FormData(paymentForm);

                // Kirim AJAX request untuk generate Snap Token
                fetch('{{ route('payment.checkout') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Snap Token berhasil dibuat, tampilkan Snap payment page
                            payButtonText.textContent = 'Membuka pembayaran...';

                            snap.pay(data.snap_token, {
                                // Callback saat pembayaran SUKSES
                                onSuccess: function (result) {
                                    console.log('Payment success:', result);
                                    // Redirect ke halaman finish
                                    window.location.href = '{{ route('payment.finish') }}?order_id={{ $order->order_id }}';
                                },

                                // Callback saat pembayaran PENDING (menunggu)
                                onPending: function (result) {
                                    console.log('Payment pending:', result);
                                    window.location.href = '{{ route('payment.unfinish') }}?order_id={{ $order->order_id }}';
                                },

                                // Callback saat pembayaran ERROR/GAGAL
                                onError: function (result) {
                                    console.log('Payment error:', result);
                                    alert('Pembayaran gagal. Silakan coba lagi.');
                                    payButton.disabled = false;
                                    payButtonText.textContent = 'Bayar Sekarang';
                                },

                                // Callback saat user MENUTUP popup tanpa menyelesaikan pembayaran
                                onClose: function () {
                                    console.log('Payment popup closed');
                                    // Biarkan user tetap di halaman
                                    payButton.disabled = false;
                                    payButtonText.textContent = 'Coba Lagi';
                                }
                            });
                        } else {
                            // Gagal membuat Snap Token
                            alert(data.message || 'Gagal memproses pembayaran');
                            payButton.disabled = false;
                            payButtonText.textContent = 'Bayar Sekarang';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                        payButton.disabled = false;
                        payButtonText.textContent = 'Bayar Sekarang';
                    });
            });

            // Jika sudah ada snap_token di database (pembayaran sebelumnya),
            // langsung tampilkan Snap popup
            @if($order->snap_token)
                payButton.addEventListener('click', function (e) {
                    e.preventDefault();

                    snap.pay('{{ $order->snap_token }}', {
                        onSuccess: function (result) {
                            window.location.href = '{{ route('payment.finish') }}?order_id={{ $order->order_id }}';
                        },
                        onPending: function (result) {
                            window.location.href = '{{ route('payment.unfinish') }}?order_id={{ $order->order_id }}';
                        },
                        onError: function (result) {
                            alert('Pembayaran gagal. Silakan coba lagi.');
                        },
                        onClose: function () {
                            // User menutup popup
                        }
                    });
                });
            @endif
    });
    </script>
@endpush