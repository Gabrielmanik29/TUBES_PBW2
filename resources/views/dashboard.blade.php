<x-app-layout>
    <x-slot name="header">
<<<<<<< HEAD
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
=======
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <div class="text-sm text-gray-500">
                Selamat datang, {{ auth()->user()->name }}!
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Welcome Card -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold mb-2">Selamat Datang di Sistem Inventaris</h3>
                            <p class="text-blue-100">Kelola peminjaman barang kampus dengan mudah dan efisien</p>
                        </div>
                        <div class="hidden md:block">
                            <i class="fas fa-boxes text-6xl text-white opacity-20"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Aksi Cepat</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('items.index') }}"
                        class="flex items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                            <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-search text-white"></i>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-800">Cari Barang</h5>
                                <p class="text-sm text-gray-600">Temukan barang yang dibutuhkan</p>
                            </div>
                        </a>

                        <a href="{{ route('my.borrowings') }}"
                           class="flex items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition">
                            <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-list text-white"></i>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-800">Peminjaman Saya</h5>
                                <p class="text-sm text-gray-600">Lihat status peminjaman</p>
                            </div>
                        </a>

                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition">
                            <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-800">Profil</h5>
                                <p class="text-sm text-gray-600">Kelola informasi akun</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-boxes text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total Barang</p>
                                <p class="text-2xl font-bold text-gray-800">--</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-check-circle text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Barang Tersedia</p>
                                <p class="text-2xl font-bold text-gray-800">--</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-clock text-orange-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Peminjaman Aktif</p>
                                <p class="text-2xl font-bold text-gray-800">--</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-tags text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Kategori</p>
                                <p class="text-2xl font-bold text-gray-800">--</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Popular Items -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Activity -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas Terbaru</h4>
                        <div class="space-y-4">
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-plus text-blue-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">Sistem inventaris aktif</p>
                                    <p class="text-xs text-gray-500">Sistem siap digunakan</p>
                                </div>
                                <span class="text-xs text-gray-400">Baru saja</span>
                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('my.borrowings') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Lihat semua aktivitas →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Popular Items -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Barang Populer</h4>
                        <div class="space-y-4">
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-100 to-purple-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-box text-blue-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">Barang belum dimuat</p>
                                    <p class="text-xs text-gray-500">Data akan muncul setelah ada peminjaman</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('items.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Jelajahi semua barang →
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Informasi Sistem</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <i class="fas fa-shield-alt text-2xl text-green-600 mb-2"></i>
                            <h5 class="font-medium text-gray-800">Aman</h5>
                            <p class="text-sm text-gray-600">Data terlindungi dengan baik</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <i class="fas fa-rocket text-2xl text-blue-600 mb-2"></i>
                            <h5 class="font-medium text-gray-800">Cepat</h5>
                            <p class="text-sm text-gray-600">Proses peminjaman instan</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <i class="fas fa-users text-2xl text-purple-600 mb-2"></i>
                            <h5 class="font-medium text-gray-800">Kolaboratif</h5>
                            <p class="text-sm text-gray-600">Dibuat untuk komunitas kampus</p>
                        </div>
                    </div>
>>>>>>> d55b9af1f343e0e3324d653f7222d76df8c70cd2
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
