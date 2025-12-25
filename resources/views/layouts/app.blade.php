<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Inventaris')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
</head>

<body class="bg-gray-50">
    @include('layouts.navigation')

    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-3"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif


    <!-- Footer - Hanya untuk non-items pages -->
    @if(!Request::is('items'))
        <footer class="bg-gray-800 text-white py-8 mt-12">
            <div class="container mx-auto px-4 text-center">
                <div class="mb-4">
                    <h3 class="text-lg font-bold mb-2">Sistem Inventaris Kampus</h3>
                    <p class="text-gray-400 text-sm">Sistem peminjaman barang terpadu - D3IF-48-04</p>
                </div>

                <div class="text-gray-400 text-sm">
                    <p class="mb-2">
                        <a href="mailto:inventaris@kampus.ac.id" class="hover:text-white">
                            <i class="fas fa-envelope mr-1"></i> inventaris@kampus.ac.id
                        </a>
                    </p>
                    <p>&copy; {{ date('Y') }} - Dibangun dengan Laravel & Tailwind CSS</p>
                    <p class="text-xs text-gray-500 mt-4">
                        <a href="/about" class="hover:text-gray-300">Tentang Sistem</a> |
                        <a href="/privacy" class="hover:text-gray-300">Kebijakan Privasi</a>
                    </p>
                </div>
            </div>
        </footer>
    @endif

    @stack('scripts')
</body>

</html>