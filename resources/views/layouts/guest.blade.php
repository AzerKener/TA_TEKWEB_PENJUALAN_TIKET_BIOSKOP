<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') — CineXpress</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex" x-data>
    <!-- Left: Auth Form -->
    <div class="flex-1 flex items-center justify-center p-6 bg-cream">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 mb-8 group">
                <div class="w-10 h-10 bg-espresso rounded-xl flex items-center justify-center shadow-espresso group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                </div>
                <span class="font-display font-bold text-2xl text-primary">Cine<span class="text-charcoal">Xpress</span></span>
            </a>

            @yield('content')
        </div>
    </div>

    <!-- Right: Decorative -->
    <div class="hidden lg:flex flex-1 items-center justify-center relative overflow-hidden hero-gradient">
        <!-- Decorative circles -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-white/5 blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-gold/10 blur-3xl"></div>
        </div>

        <div class="relative text-center text-white px-12">
            <!-- Movie poster cards decorative -->
            <div class="flex gap-3 justify-center mb-8 transform -rotate-6">
                <div class="w-24 h-36 bg-gradient-to-br from-primary-light to-primary-dark rounded-xl shadow-2xl flex items-end p-2 opacity-80">
                    <div class="w-full h-2 bg-gold/40 rounded"></div>
                </div>
                <div class="w-24 h-36 bg-gradient-to-br from-yellow-700 to-yellow-900 rounded-xl shadow-2xl mt-4 flex items-end p-2 opacity-90">
                    <div class="w-full h-2 bg-gold/40 rounded"></div>
                </div>
                <div class="w-24 h-36 bg-gradient-to-br from-gray-700 to-gray-900 rounded-xl shadow-2xl flex items-end p-2 opacity-80">
                    <div class="w-full h-2 bg-gold/40 rounded"></div>
                </div>
            </div>

            <h2 class="font-display text-4xl font-bold mb-4 leading-tight">
                Nonton Film<br>
                <span class="text-gradient-gold">Jadi Lebih Mudah</span>
            </h2>
            <p class="text-white/70 text-lg leading-relaxed max-w-sm mx-auto">
                Pesan tiket, pilih kursi favorit, dan nikmati makanan & minuman premium — semua dari genggaman tangan Anda.
            </p>

            <!-- Feature badges -->
            <div class="flex flex-wrap justify-center gap-2 mt-8">
                @foreach(['🎬 100+ Film', '🍿 F&B Premium', '💺 Pilih Kursi', '⚡ Booking Instan'] as $badge)
                    <span class="px-3 py-1.5 bg-white/10 rounded-full text-sm font-medium text-white/90 border border-white/20">{{ $badge }}</span>
                @endforeach
            </div>
        </div>
    </div>
</body>
</html>
