<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'CineXpress - Beli tiket bioskop online dengan mudah dan cepat. Nikmati pengalaman nonton film terbaik.')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CineXpress') — Sistem Penjualan Tiket Bioskop</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-cream min-h-screen" x-data>

    <!-- ── NAVBAR ── -->
    <nav class="navbar-glass sticky top-0 z-40 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <div class="w-9 h-9 bg-espresso rounded-xl flex items-center justify-center shadow-espresso group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                        </svg>
                    </div>
                    <span class="font-display font-bold text-xl text-primary">Cine<span class="text-charcoal">Xpress</span></span>
                </a>

                <!-- Desktop Nav Links -->
                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('home') }}"
                       class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('home') ? 'bg-primary/10 text-primary' : 'text-charcoal hover:bg-primary/8 hover:text-primary' }}">
                        Beranda
                    </a>
                    <a href="{{ route('movies.index') }}"
                       class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('movies.*') ? 'bg-primary/10 text-primary' : 'text-charcoal hover:bg-primary/8 hover:text-primary' }}">
                        Film
                    </a>
                    <a href="{{ route('fnb.index') }}"
                       class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('fnb.*') ? 'bg-primary/10 text-primary' : 'text-charcoal hover:bg-primary/8 hover:text-primary' }}">
                        Makanan & Minuman
                    </a>
                </div>

                <!-- Auth Actions -->
                <div class="flex items-center gap-3">
                    @auth
                        <!-- My Tickets -->
                        <a href="{{ route('my.transactions') }}" class="hidden sm:flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-medium text-charcoal hover:bg-primary/8 hover:text-primary transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                            </svg>
                            Tiket Saya
                        </a>

                        <!-- User Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.outside="open = false"
                                    class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-primary/8 transition-all duration-200">
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                                     class="w-7 h-7 rounded-lg object-cover">
                                <span class="hidden sm:block text-sm font-semibold text-charcoal max-w-[120px] truncate">
                                    {{ auth()->user()->name }}
                                </span>
                                <svg class="w-4 h-4 text-charcoal-muted transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Dropdown -->
                            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-lg border border-black/5 py-2 z-50">

                                <div class="px-4 py-2 border-b border-black/5">
                                    <p class="text-xs font-semibold text-charcoal-muted uppercase tracking-wider">{{ ucfirst(auth()->user()->role) }}</p>
                                    <p class="text-sm font-bold text-charcoal truncate">{{ auth()->user()->name }}</p>
                                </div>

                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-charcoal hover:bg-primary/8 hover:text-primary transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                        Dashboard Admin
                                    </a>
                                @endif

                                <a href="{{ route('my.transactions') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-charcoal hover:bg-primary/8 hover:text-primary transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                    Riwayat Pemesanan
                                </a>

                                <div class="border-t border-black/5 mt-1 pt-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn-ghost text-sm px-4 py-2">Masuk</a>
                        <a href="{{ route('register') }}" class="btn-primary text-sm px-4 py-2">Daftar</a>
                    @endauth

                    <!-- Mobile menu button -->
                    <button class="md:hidden p-2 rounded-xl hover:bg-primary/8 transition-colors" x-data @click="$dispatch('toggle-mobile-menu')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-data="{ open: false }" @toggle-mobile-menu.window="open = !open">
            <div x-show="open" x-transition class="md:hidden border-t border-primary/10 bg-cream-light py-3 px-4 space-y-1">
                <a href="{{ route('home') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('home') ? 'bg-primary text-white' : 'text-charcoal hover:bg-primary/8' }}">Beranda</a>
                <a href="{{ route('movies.index') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('movies.*') ? 'bg-primary text-white' : 'text-charcoal hover:bg-primary/8' }}">Film</a>
                <a href="{{ route('fnb.index') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('fnb.*') ? 'bg-primary text-white' : 'text-charcoal hover:bg-primary/8' }}">Makanan & Minuman</a>
                @auth
                    <a href="{{ route('my.transactions') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium text-charcoal hover:bg-primary/8">Tiket Saya</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- ── FLASH MESSAGES ── -->
    @if(session('success') || session('error') || session('warning'))
        <div class="fixed top-20 right-4 z-50 space-y-2" x-data>
            @if(session('success'))
                <div data-flash class="toast toast-success animate-slide-down">
                    <svg class="w-5 h-5 flex-shrink-0 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div data-flash class="toast toast-error animate-slide-down">
                    <svg class="w-5 h-5 flex-shrink-0 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if(session('warning'))
                <div data-flash class="toast toast-warning animate-slide-down">
                    <svg class="w-5 h-5 flex-shrink-0 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif
        </div>
    @endif

    <!-- ── MAIN CONTENT ── -->
    <main>
        @yield('content')
    </main>

    <!-- ── FOOTER ── -->
    <footer class="bg-charcoal text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand -->
                <div class="md:col-span-1">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-9 h-9 bg-espresso rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                        </div>
                        <span class="font-display font-bold text-xl">Cine<span class="text-gold">Xpress</span></span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed">Platform terpercaya untuk pembelian tiket bioskop online. Cepat, mudah, dan aman.</p>
                </div>

                <!-- Links -->
                <div>
                    <h4 class="font-semibold text-gold mb-3 text-sm uppercase tracking-wider">Film</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('movies.index', ['status' => 'now_playing']) }}" class="hover:text-white transition-colors">Sedang Tayang</a></li>
                        <li><a href="{{ route('movies.index', ['status' => 'coming_soon']) }}" class="hover:text-white transition-colors">Segera Tayang</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold text-gold mb-3 text-sm uppercase tracking-wider">Layanan</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('fnb.index') }}" class="hover:text-white transition-colors">Menu F&B</a></li>
                        @auth
                        <li><a href="{{ route('my.transactions') }}" class="hover:text-white transition-colors">Tiket Saya</a></li>
                        @endauth
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold text-gold mb-3 text-sm uppercase tracking-wider">Informasi</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><span>Jl. Bioskop No. 21, Jakarta</span></li>
                        <li><span>Buka: 09.00 — 23.00 WIB</span></li>
                        <li><span>info@cinexpress.id</span></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-white/10 mt-8 pt-6 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-500 text-sm">© {{ date('Y') }} CineXpress. UAP Teknologi Web — Kelompok 4.</p>
                <div class="flex gap-4 text-xs text-gray-500">
                    <span>Laravel {{ app()->version() }}</span>
                    <span>•</span>
                    <span>The Espresso Theme</span>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
