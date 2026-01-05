@extends('layouts.auth')

@section('title', 'Daftar - Sistem Inventaris')

@section('content')
    <div class="slide-in">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-3xl card-shadow overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2">

                    <!-- Left Column: Branding -->
                    <div class="gradient-bg p-8 lg:p-12 text-white">

                        <!-- Back Button -->
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center text-white opacity-90 hover:opacity-100 mb-8 transition">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Login
                        </a>

                        <!-- Branding -->
                        <div class="mb-10">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 bg-white bg-opacity-20 rounded-2xl mb-6">
                                <i class="fas fa-user-plus text-3xl"></i>
                            </div>
                            <h1 class="text-4xl font-bold mb-4">Bergabung dengan Kami</h1>
                            <p class="text-xl opacity-90">
                                Daftar sekarang untuk mulai meminjam barang kampus dengan mudah.
                            </p>
                        </div>

                        <!-- Benefits -->
                        <div class="space-y-6">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mt-1">
                                    <div
                                        class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                        <i class="fas fa-rocket"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold">Akses Cepat</h3>
                                    <p class="opacity-90">Proses peminjaman barang lebih efisien</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0 mt-1">
                                    <div
                                        class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                        <i class="fas fa-bell"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold">Notifikasi Real-time</h3>
                                    <p class="opacity-90">Status peminjaman langsung di notifikasi</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0 mt-1">
                                    <div
                                        class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold">Riwayat Terorganisir</h3>
                                    <p class="opacity-90">Semua riwayat tersimpan rapi dalam satu akun</p>
                                </div>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="mt-12 grid grid-cols-2 gap-4">
                            <div class="bg-white bg-opacity-10 p-4 rounded-xl">
                                <div class="text-2xl font-bold">48 Jam</div>
                                <div class="text-sm opacity-90">Rata-rata proses</div>
                            </div>
                            <div class="bg-white bg-opacity-10 p-4 rounded-xl">
                                <div class="text-2xl font-bold">99%</div>
                                <div class="text-sm opacity-90">Kepuasan pengguna</div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Register Form -->
                    <div class="p-8 lg:p-12">

                        <!-- Form Header -->
                        <div class="mb-8">
                            <h2 class="text-3xl font-bold text-gray-800">Buat Akun Baru</h2>
                            <p class="text-gray-600 mt-2">
                                Isi data diri Anda untuk mulai menggunakan sistem
                            </p>
                        </div>

                        <!-- Validation Errors -->
                        @if ($errors->any())
                            <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-700 rounded-xl">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-exclamation-circle mr-3"></i>
                                    <span class="font-semibold">Perbaiki kesalahan berikut:</span>
                                </div>
                                <ul class="list-disc list-inside ml-7">
                                    @foreach ($errors->all() as $error)
                                        <li class="text-sm">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Register Form -->
                        <form method="POST" action="{{ route('register') }}" id="registerForm" class="space-y-6">
                            @csrf

                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-user mr-2 text-blue-500"></i>Nama Lengkap
                                </label>
                                <div class="relative">
                                    <input id="name" type="text" name="name" value="{{ old('name') }}" required
                                        autocomplete="name"
                                        class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl input-focus"
                                        placeholder="John Doe">
                                    <div class="absolute left-4 top-3.5 text-gray-400">
                                        <i class="fas fa-address-card"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-envelope mr-2 text-blue-500"></i>Alamat Email
                                </label>
                                <div class="relative">
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                        autocomplete="email"
                                        class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl input-focus"
                                        placeholder="nama@email.com">
                                    <div class="absolute left-4 top-3.5 text-gray-400">
                                        <i class="fas fa-at"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-lock mr-2 text-blue-500"></i>Kata Sandi
                                </label>
                                <div class="relative">
                                    <input id="password" type="password" name="password" required
                                        autocomplete="new-password"
                                        class="w-full pl-12 pr-12 py-3 border border-gray-300 rounded-xl input-focus"
                                        placeholder="••••••••">
                                    <div class="absolute left-4 top-3.5 text-gray-400">
                                        <i class="fas fa-key"></i>
                                    </div>
                                    <button type="button" onclick="togglePassword('password', 'registerPasswordToggle')"
                                        class="absolute right-4 top-3.5 text-gray-400 hover:text-gray-600">
                                        <i id="registerPasswordToggle" class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">
                                    Minimal 8 karakter dengan kombinasi huruf dan angka
                                </p>
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-lock mr-2 text-blue-500"></i>Konfirmasi Kata Sandi
                                </label>
                                <div class="relative">
                                    <input id="password_confirmation" type="password" name="password_confirmation" required
                                        autocomplete="new-password"
                                        class="w-full pl-12 pr-12 py-3 border border-gray-300 rounded-xl input-focus"
                                        placeholder="••••••••">
                                    <div class="absolute left-4 top-3.5 text-gray-400">
                                        <i class="fas fa-key"></i>
                                    </div>
                                    <button type="button"
                                        onclick="togglePassword('password_confirmation', 'registerConfirmToggle')"
                                        class="absolute right-4 top-3.5 text-gray-400 hover:text-gray-600">
                                        <i id="registerConfirmToggle" class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>





                            <!-- Role Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-user-tag mr-2 text-blue-500"></i>Jenis Akun
                                </label>

                                <div class="grid grid-cols-2 gap-4" role="radiogroup">
                                    <!-- User -->
                                    <div class="relative">
                                        <input type="radio" id="role_user" name="role" value="user" checked
                                            class="role-radio sr-only">
                                        <label for="role_user" data-role="user"
                                            class="role-label role-user flex flex-col items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 select-none bg-blue-50 border-blue-500 ring-2 ring-blue-200"
                                            onclick="selectRole('user')">
                                            <i class="fas fa-user text-2xl text-blue-500 mb-2 role-icon"></i>
                                            <span class="font-medium role-text text-blue-700">Anggota</span>
                                            <span class="text-xs text-gray-500 mt-1">Meminjam barang</span>
                                        </label>
                                    </div>

                                    <!-- Admin -->
                                    <div class="relative">
                                        <input type="radio" id="role_admin" name="role" value="admin"
                                            class="role-radio sr-only">
                                        <label for="role_admin" data-role="admin"
                                            class="role-label role-admin flex flex-col items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 select-none"
                                            onclick="selectRole('admin')">
                                            <i class="fas fa-user-shield text-2xl text-gray-400 mb-2 role-icon"></i>
                                            <span class="font-medium role-text text-gray-600">Admin</span>
                                            <span class="text-xs text-gray-500 mt-1">Mengelola sistem</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Role Selection Help Text -->
                                <p class="text-xs text-gray-500 mt-2 flex items-center">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Pilih "Admin" jika Anda ingin mengelola sistem, atau "Anggota" untuk meminjam barang
                                </p>
                            </div>

                            <!-- Role Selection Styles -->
                            <style>
                                .role-label:hover {
                                    border-color: #3b82f6 !important;
                                    background-color: #eff6ff !important;
                                }

                                .role-label.active-user {
                                    background-color: #dbeafe !important;
                                    border-color: #3b82f6 !important;
                                    border-width: 2px !important;
                                    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
                                }

                                .role-label.active-user .role-icon {
                                    color: #3b82f6 !important;
                                }

                                .role-label.active-user .role-text {
                                    color: #1e40af !important;
                                }

                                .role-label.active-admin {
                                    background-color: #f3e8ff !important;
                                    border-color: #9333ea !important;
                                    border-width: 2px !important;
                                    box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.1) !important;
                                }

                                .role-label.active-admin .role-icon {
                                    color: #9333ea !important;
                                }

                                .role-label.active-admin .role-text {
                                    color: #7c3aed !important;
                                }

                                .role-label.inactive {
                                    background-color: #ffffff !important;
                                    border-color: #e5e7eb !important;
                                    border-width: 2px !important;
                                }

                                .role-label.inactive .role-icon {
                                    color: #9ca3af !important;
                                }

                                .role-label.inactive .role-text {
                                    color: #6b7280 !important;
                                }
                            </style>

                            <!-- Terms -->
                            <div class="flex items-start">
                                <input id="terms" type="checkbox" required
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="terms" class="ml-3 text-sm text-gray-700">
                                    Saya setuju dengan
                                    <a href="#" class="text-blue-600 font-medium hover:text-blue-800">Syarat & Ketentuan</a>
                                    dan
                                    <a href="#" class="text-blue-600 font-medium hover:text-blue-800">Kebijakan Privasi</a>
                                </label>
                            </div>

                            <!-- Submit -->
                            <button type="submit"
                                class="w-full py-4 gradient-bg text-white font-bold rounded-xl shadow-lg hover:opacity-90 hover:shadow-xl transition">
                                <i class="fas fa-user-plus mr-3"></i>Daftar Sekarang
                            </button>

                            <!-- Login Link -->
                            <div class="text-center pt-6 border-t border-gray-200">
                                <p class="text-gray-600">
                                    Sudah punya akun?
                                    <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:text-blue-800 ml-1">
                                        Masuk di sini
                                    </a>
                                </p>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center text-gray-500 text-sm">
                © {{ date('Y') }} Sistem Inventaris Kampus - Kelompok 5 D3IF-48-04
            </div>
        </div>
    </div>


    <script>
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('password_confirmation');

        function validatePassword() {
            if (password.value !== confirmPassword.value) {
                confirmPassword.classList.add('border-red-500');
                confirmPassword.classList.remove('border-green-500');
            } else {
                confirmPassword.classList.remove('border-red-500');
                confirmPassword.classList.add('border-green-500');
            }

            const strength = checkPasswordStrength(password.value);
            updateStrengthIndicator(strength);
        }

        function checkPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            return strength;
        }

        function updateStrengthIndicator(strength) {
            const indicator = document.getElementById('passwordStrength');
            if (!indicator) return;

            indicator.className = 'text-xs mt-1';

            if (strength === 0) {
                indicator.textContent = '';
            } else if (strength <= 2) {
                indicator.textContent = 'Kata sandi lemah';
                indicator.classList.add('text-red-500');
            } else if (strength === 3) {
                indicator.textContent = 'Kata sandi cukup';
                indicator.classList.add('text-yellow-500');
            } else {
                indicator.textContent = 'Kata sandi kuat';
                indicator.classList.add('text-green-500');
            }
        }



        // Simplified role selection functionality
        function selectRole(role) {
            // Check the radio button
            document.getElementById('role_' + role).checked = true;

            // Update visual states
            const userLabel = document.querySelector('[data-role="user"]');
            const adminLabel = document.querySelector('[data-role="admin"]');

            // Remove all active classes
            userLabel.classList.remove('active-user', 'active-admin');
            adminLabel.classList.remove('active-user', 'active-admin');
            userLabel.classList.add('inactive');
            adminLabel.classList.add('inactive');

            // Add appropriate active class based on selection
            if (role === 'user') {
                userLabel.classList.remove('inactive');
                userLabel.classList.add('active-user');
            } else if (role === 'admin') {
                adminLabel.classList.remove('inactive');
                adminLabel.classList.add('active-admin');
            }
        }


        document.addEventListener('DOMContentLoaded', function () {
            const div = password.parentElement.parentElement;
            const indicator = document.createElement('p');

            indicator.id = 'passwordStrength';
            indicator.className = 'text-xs mt-1';
            div.appendChild(indicator);

            password.addEventListener('input', validatePassword);
            confirmPassword.addEventListener('input', validatePassword);

            // Initialize role selection to 'user' (default)
            selectRole('user');
        });
    </script>
@endsection