@extends('layouts.auth')

@section('title', 'Login - Sistem Inventaris')

@section('content')
<div class="slide-in">
    <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            
            <!-- Left Column: Branding & Info -->
            <div class="bg-white p-8 lg:p-12 rounded-3xl card-shadow">
                
                <!-- Logo & Brand -->
                <div class="text-center mb-10">
                    <div class="inline-flex items-center justify-center w-20 h-20 gradient-bg rounded-2xl mb-6 animate-float">
                        <i class="fas fa-box text-4xl text-white"></i>
                    </div>
                    <h1 class="text-4xl font-bold gradient-text">Sistem Inventaris Kampus</h1>
                    <p class="text-gray-600 mt-3">Digitalisasi peminjaman barang dengan sistem terintegrasi</p>
                </div>
                
                <!-- Features -->
                <div class="space-y-6 mb-10">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-800">Pinjam Barang Mudah</h3>
                            <p class="text-gray-600">Ajukan peminjaman kapan saja, di mana saja</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-bolt text-blue-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-800">Proses Cepat</h3>
                            <p class="text-gray-600">Persetujuan online tanpa antri</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-shield-alt text-purple-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-800">Aman & Terpercaya</h3>
                            <p class="text-gray-600">Dilengkapi autentikasi dan enkripsi data</p>
                        </div>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <div class="text-2xl font-bold text-blue-600">500+</div>
                        <div class="text-sm text-gray-600">Barang</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <div class="text-2xl font-bold text-green-600">1.2K+</div>
                        <div class="text-sm text-gray-600">Pengguna</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <div class="text-2xl font-bold text-purple-600">3.5K+</div>
                        <div class="text-sm text-gray-600">Peminjaman</div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column: Login Form -->
            <div class="bg-white p-8 lg:p-12 rounded-3xl card-shadow">
                
                <!-- Form Header -->
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-800">Masuk ke Akun</h2>
                    <p class="text-gray-600 mt-2">Selamat datang kembali! Silakan masuk untuk melanjutkan</p>
                </div>
                
                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-700 rounded-xl">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-3"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    </div>
                @endif
                
                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-700 rounded-xl">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-exclamation-circle mr-3"></i>
                            <span class="font-semibold">Ada kesalahan</span>
                        </div>
                        <ul class="list-disc list-inside ml-7">
                            @foreach ($errors->all() as $error)
                                <li class="text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" id="loginForm" class="space-y-6">
                    @csrf
                    
                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2 text-blue-500"></i>Alamat Email
                        </label>
                        <div class="relative">
                            <input 
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="email"
                                class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl input-focus transition duration-200"
                                placeholder="nama@email.com"
                            >
                            <div class="absolute left-4 top-3.5 text-gray-400">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Password Input -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-lock mr-2 text-blue-500"></i>Kata Sandi
                            </label>
                            @if (Route::has('password.request'))
                                <a 
                                    href="{{ route('password.request') }}"
                                    class="text-sm text-blue-600 hover:text-blue-800 font-medium transition"
                                >
                                    Lupa kata sandi?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <input 
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                class="w-full pl-12 pr-12 py-3 border border-gray-300 rounded-xl input-focus transition duration-200"
                                placeholder="••••••••"
                            >
                            <div class="absolute left-4 top-3.5 text-gray-400">
                                <i class="fas fa-key"></i>
                            </div>
                            <button 
                                type="button"
                                onclick="togglePassword('password', 'passwordToggleIcon')"
                                class="absolute right-4 top-3.5 text-gray-400 hover:text-gray-600 transition"
                            >
                                <i id="passwordToggleIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input 
                            id="remember_me"
                            type="checkbox"
                            name="remember"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        >
                        <label for="remember_me" class="ml-3 block text-sm text-gray-700">
                            Ingat saya di perangkat ini
                        </label>
                    </div>
                    
                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        class="w-full py-4 gradient-bg hover:opacity-90 text-white font-bold rounded-xl transition duration-300 shadow-lg hover:shadow-xl"
                    >
                        <i class="fas fa-sign-in-alt mr-3"></i>Masuk ke Sistem
                    </button>

                    <!-- Demo Credentials -->
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <h4 class="font-bold text-gray-700 mb-2 text-sm">Demo Credentials:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-gray-600">Admin:</p>
                                <p class="font-mono text-xs bg-gray-100 p-2 rounded">admin@example.com</p>
                                <p class="font-mono text-xs bg-gray-100 p-2 rounded">password</p>
                            </div>
                            <div>
                                <p class="text-gray-600">User:</p>
                                <p class="font-mono text-xs bg-gray-100 p-2 rounded">user@example.com</p>
                                <p class="font-mono text-xs bg-gray-100 p-2 rounded">password</p>
                            </div>
                        </div>
                    </div>

                    <!-- Register Link -->
                    <div class="text-center pt-6 border-t border-gray-200">
                        <p class="text-gray-600">
                            Belum punya akun?
                            <a 
                                href="{{ route('register') }}"
                                class="font-bold text-blue-600 hover:text-blue-800 ml-2 transition"
                            >
                                Daftar Sekarang
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="mt-8 text-center text-gray-500 text-sm">
            <p>© {{ date('Y') }} Sistem Inventaris Kampus - Kelompok 5 D3IF-48-04</p>
            <p class="mt-1">Gabriel, Excellence, Nafiza, Fabio</p>
        </div>
    </div>
</div>
@endsection
