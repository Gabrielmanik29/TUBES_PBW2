@extends('layouts.auth')

@section('title', 'Lupa Password - Sistem Inventaris')

@section('content')
<div class="slide-in">
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-3xl card-shadow overflow-hidden">
            <!-- Header -->
            <div class="gradient-bg p-8 text-white text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white bg-opacity-20 rounded-2xl mb-6">
                    <i class="fas fa-key text-3xl"></i>
                </div>
                <h2 class="text-3xl font-bold">Reset Kata Sandi</h2>
                <p class="opacity-90 mt-2">Masukkan email Anda untuk mengatur ulang kata sandi</p>
            </div>
            
            <!-- Content -->
            <div class="p-8">
                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-700 rounded-xl">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-3"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    </div>
                @endif
                
                <!-- Instructions -->
                <div class="mb-6 p-4 bg-blue-50 border border-blue-100 rounded-xl">
                    <div class="flex">
                        <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                        <p class="text-sm text-blue-700">
                            Kami akan mengirimkan link reset password ke email Anda. Pastikan email yang Anda masukkan sudah terdaftar.
                        </p>
                    </div>
                </div>
                
                <!-- Form -->
                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf
                    
                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2 text-blue-500"></i>Alamat Email
                        </label>
                        <div class="relative">
                            <input id="email" 
                                    type="email" 
                                    name="email" 
                                    value="{{ old('email') }}" 
                                    required 
                                    autofocus
                                    class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl input-focus transition duration-200"
                                    placeholder="nama@email.com">
                            <div class="absolute left-4 top-3.5 text-gray-400">
                                <i class="fas fa-at"></i>
                            </div>
                        </div>
                        @error('email')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full py-4 gradient-bg hover:opacity-90 text-white font-bold rounded-xl transition duration-300 shadow-lg hover:shadow-xl">
                        <i class="fas fa-paper-plane mr-3"></i>Kirim Link Reset
                    </button>
                    
                    <!-- Back to Login -->
                    <div class="text-center pt-4">
                        <a href="{{ route('login') }}" 
                            class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali ke halaman login
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Support -->
        <div class="mt-6 p-4 bg-gray-50 rounded-xl border border-gray-200 text-center">
            <p class="text-sm text-gray-600">
                Butuh bantuan? Hubungi admin di 
                <a href="mailto:admin@inventaris.com" class="text-blue-600 font-medium">admin@inventaris.com</a>
            </p>
        </div>
        
        <!-- Footer -->
        <div class="mt-8 text-center text-gray-500 text-sm">
            <p>© {{ date('Y') }} Sistem Inventaris Kampus</p>
        </div>
    </div>
</div>
@endsection