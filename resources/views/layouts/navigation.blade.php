<nav x-data="{ open: false }" class="bg-white shadow-lg border-b border-gray-200">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo & Brand -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                        <div
                            class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center group-hover:shadow-lg transition">
                            <i class="fas fa-boxes text-white text-lg"></i>
                        </div>
                        <div class="hidden sm:block">
                            <h1 class="text-xl font-bold text-gray-800">Inventaris</h1>
                            <p class="text-xs text-gray-500 -mt-1">Sistem Peminjaman</p>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden lg:ml-10 lg:flex lg:space-x-1">
                    <!-- Items (Gabriel's Task) -->
                    <x-nav-link :href="route('items.index')" :active="request()->routeIs('items.*')"
                        class="flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-green-50 hover:text-green-700">
                        <i class="fas fa-box mr-2"></i>
                        Barang
                    </x-nav-link>

                    <!-- My Borrowings -->
                    <x-nav-link :href="route('my.borrowings')" :active="request()->routeIs('my.borrowings')"
                        class="flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-orange-50 hover:text-orange-700">
                        <i class="fas fa-handshake mr-2"></i>
                        Peminjaman
                    </x-nav-link>

                    <!-- Admin Menu (for Admin users) -->
                    @if(Auth::check() && Auth::user()->role === 'admin')
                        <div class="relative" x-data="{ adminOpen: false }">
                            <button @click="adminOpen = !adminOpen"
                                :class="{'bg-purple-100 text-purple-700': adminOpen, 'text-gray-700 hover:bg-purple-50': !adminOpen}"
                                class="flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                                <i class="fas fa-crown mr-2"></i>
                                Admin
                                <svg class="ml-2 w-4 h-4 transition-transform" :class="{'rotate-180': adminOpen}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div x-show="adminOpen" @click.away="adminOpen = false" x-transition
                                class="absolute top-full left-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                                <a href="{{ route('admin.items.create') }}"
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700">
                                    <i class="fas fa-boxes mr-3 text-purple-500"></i>
                                    Kelola Barang
                                </a>
                                <a href="{{ route('admin.categories.index') }}"
                                    class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700">
                                    <i class="fas fa-tags"></i>
                                    Kelola Kategori
                                </a>
                                <a href="{{ route('admin.peminjaman') }}"
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700">
                                    <i class="fas fa-clipboard-check mr-3 text-purple-500"></i>
                                    Approve Peminjaman
                                </a>
                                <a href="{{ route('admin.peminjaman') }}"
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700">
                                    <i class="fas fa-undo mr-3 text-purple-500"></i>
                                    Pengembalian
                                </a>
                                <hr class="my-2 border-gray-200">
                                <a href="#"
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700">
                                    <i class="fas fa-chart-bar mr-3 text-purple-500"></i>
                                    Laporan
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- User Menu -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Notifications -->
                <button
                    class="p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-lg transition mr-2 relative">
                    <i class="fas fa-bell text-lg"></i>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>

                <!-- User Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="flex items-center p-2 text-sm rounded-lg border-2 border-transparent hover:border-gray-200 hover:bg-gray-50 transition-all duration-200">
                            <div
                                class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center mr-3">
                                <span
                                    class="text-white font-semibold text-sm">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                            </div>
                            <div class="text-left">
                                <div class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</div>
                                <div class="text-xs text-gray-500 capitalize">{{ Auth::user()->role ?? 'User' }}</div>
                            </div>
                            <svg class="ml-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content" class="py-2">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                            <div class="mt-2">
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-user mr-1"></i>
                                    {{ ucfirst(Auth::user()->role ?? 'User') }}
                                </span>
                            </div>
                        </div>

                        <x-dropdown-link :href="route('profile.edit')" class="flex items-center">
                            <i class="fas fa-user-circle mr-3 text-gray-400"></i>
                            Profile
                        </x-dropdown-link>

                        <x-dropdown-link href="#" class="flex items-center text-gray-500 cursor-not-allowed">
                            <i class="fas fa-question-circle mr-3 text-gray-400"></i>
                            Bantuan
                            <span class="ml-auto text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Soon</span>
                        </x-dropdown-link>

                        <hr class="my-2 border-gray-200">

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="flex items-center text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt mr-3"></i>
                                Keluar
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile menu button -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-lg text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-gray-200 bg-white">
        <div class="px-4 pt-2 pb-3 space-y-1">
            <!-- Mobile Items -->
            <x-responsive-nav-link :href="route('items.index')" :active="request()->routeIs('items.*')"
                class="flex items-center px-3 py-2 rounded-lg text-base font-medium">
                <i class="fas fa-box mr-3"></i>
                Barang
            </x-responsive-nav-link>

            <!-- Mobile Borrowing -->
            <x-responsive-nav-link :href="route('my.borrowings')" :active="request()->routeIs('my.borrowings')"
                class="flex items-center px-3 py-2 rounded-lg text-base font-medium">
                <i class="fas fa-handshake mr-3"></i>
                Peminjaman
            </x-responsive-nav-link>

            <!-- Mobile Admin Menu -->
            @if(Auth::check() && Auth::user()->role === 'admin')
                <div class="px-3 py-2">
                    <div class="flex items-center text-purple-700 font-medium">
                        <i class="fas fa-crown mr-3"></i>
                        Menu Admin
                    </div>
                    <div class="ml-6 mt-2 space-y-1">
                        <a href="{{ route('admin.items.create') }}"
                            class="flex items-center px-3 py-2 text-sm text-gray-600 hover:bg-purple-50 hover:text-purple-700 rounded-lg">
                            <i class="fas fa-boxes mr-3"></i>
                            Kelola Barang
                        </a>
                        <a href="{{ route('admin.categories.index') }}"
                            class="flex items-center px-3 py-2 text-sm text-gray-600 hover:bg-purple-50 hover:text-purple-700 rounded-lg"
                            <i class="fas fa-tags mr-3"></i>
                            Kelola Kategori
                        </a>
                        <a href="{{ route('admin.peminjaman') }}"
                            class="flex items-center px-3 py-2 text-sm text-gray-600 hover:bg-purple-50 hover:text-purple-700 rounded-lg">
                            <i class="fas fa-clipboard-check mr-3"></i>
                            Approve Peminjaman
                        </a>
                        <a href="{{ route('admin.peminjaman') }}"
                            class="flex items-center px-3 py-2 text-sm text-gray-600 hover:bg-purple-50 hover:text-purple-700 rounded-lg">
                            <i class="fas fa-undo mr-3"></i>
                            Pengembalian
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Mobile User Info -->
        <div class="pt-4 pb-3 border-t border-gray-200">
            <div class="px-4">
                <div class="flex items-center">
                    <div
                        class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center mr-3">
                        <span class="text-white font-semibold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                    </div>
                    <div>
                        <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="text-sm text-gray-500">{{ Auth::user()->email }}</div>
                        <div class="mt-1">
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst(Auth::user()->role ?? 'User') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="flex items-center">
                    <i class="fas fa-user-circle mr-3"></i>
                    Profile
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();"
                        class="flex items-center text-red-600">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        Keluar
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>