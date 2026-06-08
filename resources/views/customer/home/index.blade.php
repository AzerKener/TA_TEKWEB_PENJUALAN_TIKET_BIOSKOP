@extends('layouts.app')

@section('title', 'Beranda')
@section('meta_description', 'CineXpress - Beli tiket bioskop online. Pilih film favorit, pesan kursi, dan nikmati pengalaman nonton terbaik.')

@section('content')

{{-- ═══ HERO SECTION ═══ --}}
@if($featuredMovie)
<section class="relative min-h-[85vh] flex items-end overflow-hidden hero-gradient">

    {{-- Background blur poster --}}
    <div class="absolute inset-0">
        @if($featuredMovie->poster_image)
            <img src="{{ $featuredMovie->poster_url }}" alt="" class="w-full h-full object-cover opacity-20 scale-110 blur-sm">
        @endif
        <div class="absolute inset-0 hero-gradient opacity-90"></div>
    </div>

    {{-- Floating particles --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        @for($i = 0; $i < 6; $i++)
            <div class="absolute w-1 h-1 bg-gold/40 rounded-full animate-pulse-slow"
                 style="top: {{ rand(10, 90) }}%; left: {{ rand(5, 95) }}%; animation-delay: {{ $i * 0.5 }}s;"></div>
        @endfor
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12 pt-24 w-full">
        <div class="grid lg:grid-cols-2 gap-8 items-end">

            {{-- Hero Text --}}
            <div class="animate-slide-up">
                <div class="flex items-center gap-2 mb-4">
                    <span class="badge bg-gold/20 text-gold border border-gold/30 text-xs uppercase tracking-wider font-bold">
                        🎬 Film Unggulan
                    </span>
                    <span class="badge badge-green text-xs">
                        ● Sedang Tayang
                    </span>
                </div>

                <h1 class="font-display text-4xl md:text-6xl font-black text-white leading-tight mb-4">
                    {{ $featuredMovie->title }}
                </h1>

                <div class="flex flex-wrap items-center gap-3 mb-5 text-sm text-white/70">
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4 text-gold" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        {{ $featuredMovie->imdb_rating ?? 'N/A' }}/10
                    </span>
                    <span>{{ $featuredMovie->duration_formatted }}</span>
                    <span>{{ $featuredMovie->genre_list }}</span>
                    <span class="px-2 py-0.5 border border-white/30 rounded text-xs">{{ $featuredMovie->rating }}</span>
                </div>

                <p class="text-white/70 text-base leading-relaxed mb-8 max-w-lg line-clamp-3">
                    {{ $featuredMovie->synopsis }}
                </p>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('booking.schedule', $featuredMovie) }}" class="btn-gold btn-lg gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        Beli Tiket Sekarang
                    </a>
                    <a href="{{ route('movies.show', $featuredMovie->slug) }}"
                       class="btn border-2 border-white/30 text-white hover:bg-white/10 btn-lg backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Lihat Detail
                    </a>
                </div>
            </div>

            {{-- Hero Poster --}}
            <div class="hidden lg:flex justify-end">
                <div class="relative">
                    <div class="w-64 h-96 rounded-2xl overflow-hidden shadow-2xl ring-4 ring-white/20 transform rotate-3 hover:rotate-0 transition-transform duration-500">
                        @if($featuredMovie->poster_image)
                            <img src="{{ $featuredMovie->poster_url }}" alt="{{ $featuredMovie->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-primary-light to-primary-dark flex items-center justify-center">
                                <svg class="w-16 h-16 text-white/40" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                            </div>
                        @endif
                    </div>
                    {{-- Floating badge --}}
                    <div class="absolute -top-3 -right-3 bg-gold text-charcoal font-bold text-xs px-3 py-1.5 rounded-full shadow-lg">
                        ⭐ {{ $featuredMovie->imdb_rating ?? '?' }}/10
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ═══ STATS BAR ═══ --}}
<div class="bg-charcoal text-white py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
            @foreach([
                ['label' => 'Film Tayang', 'value' => $nowPlaying->count(), 'icon' => '🎬'],
                ['label' => 'Studio Premium', 'value' => '5', 'icon' => '🏛️'],
                ['label' => 'Kursi Tersedia', 'value' => '500+', 'icon' => '💺'],
                ['label' => 'Pilihan F&B', 'value' => '30+', 'icon' => '🍿'],
            ] as $stat)
                <div class="flex items-center justify-center gap-2">
                    <span class="text-xl">{{ $stat['icon'] }}</span>
                    <div class="text-left">
                        <div class="font-display font-bold text-lg text-gold">{{ $stat['value'] }}</div>
                        <div class="text-xs text-gray-400">{{ $stat['label'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ═══ NOW PLAYING ═══ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="section-title">Sedang Tayang</h2>
            <p class="section-subtitle mt-1">Film terbaik yang sedang hadir di bioskop</p>
        </div>
        <a href="{{ route('movies.index', ['status' => 'now_playing']) }}" class="btn-outline">
            Lihat Semua
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 lg:gap-6">
        @foreach($nowPlaying as $movie)
            <div class="card-movie group" onclick="window.location='{{ route('movies.show', $movie->slug) }}'">
                {{-- Poster --}}
                <div class="relative aspect-[2/3] overflow-hidden bg-cream-dark">
                    @if($movie->poster_image)
                        <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" class="movie-poster">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-primary/20 to-primary/40 flex items-center justify-center">
                            <svg class="w-12 h-12 text-primary/40" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                        </div>
                    @endif

                    {{-- Overlay --}}
                    <div class="absolute inset-0 poster-overlay opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-3">
                        <a href="{{ route('booking.schedule', $movie) }}"
                           class="btn-gold w-full text-xs py-2 justify-center"
                           onclick="event.stopPropagation()">
                            🎟️ Beli Tiket
                        </a>
                    </div>

                    {{-- Rating badge --}}
                    <div class="absolute top-2 left-2">
                        <span class="badge bg-black/60 text-white border-0 text-xs backdrop-blur-sm">{{ $movie->rating }}</span>
                    </div>

                    @if($movie->imdb_rating)
                    <div class="absolute top-2 right-2">
                        <span class="badge bg-yellow-500/90 text-charcoal border-0 text-xs backdrop-blur-sm font-bold">⭐ {{ $movie->imdb_rating }}</span>
                    </div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="p-3">
                    <h3 class="font-semibold text-sm text-charcoal leading-tight line-clamp-2 mb-1.5 group-hover:text-primary transition-colors">
                        {{ $movie->title }}
                    </h3>
                    <div class="flex items-center justify-between text-xs text-charcoal-muted">
                        <span>{{ $movie->duration_formatted }}</span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            {{ $movie->average_rating ?: 'N/A' }}
                        </span>
                    </div>
                    <div class="mt-2 flex flex-wrap gap-1">
                        @foreach(array_slice($movie->genre ?? [], 0, 2) as $genre)
                            <span class="badge-primary text-xs">{{ $genre }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- ═══ COMING SOON ═══ --}}
@if($comingSoon->count() > 0)
<section class="bg-charcoal py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="section-title text-white">Segera Tayang</h2>
                <p class="section-subtitle mt-1 text-gray-400">Film yang akan segera hadir — tandai kalendermu!</p>
            </div>
            <a href="{{ route('movies.index', ['status' => 'coming_soon']) }}" class="btn border-2 border-white/20 text-white hover:bg-white/10">
                Lihat Semua
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4">
            @foreach($comingSoon as $movie)
                <a href="{{ route('movies.show', $movie->slug) }}"
                   class="group block relative rounded-2xl overflow-hidden bg-charcoal-light hover:scale-105 transition-transform duration-300">
                    <div class="aspect-[2/3] bg-gradient-to-br from-primary/30 to-charcoal-light overflow-hidden">
                        @if($movie->poster_image)
                            <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" class="movie-poster opacity-80 group-hover:opacity-100">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-10 h-10 text-white/20" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                    </div>
                    <div class="absolute bottom-0 p-2.5 w-full">
                        <p class="text-white text-xs font-semibold line-clamp-2 leading-tight">{{ $movie->title }}</p>
                        <p class="text-gold text-xs mt-1 font-medium">{{ $movie->release_date->format('d M Y') }}</p>
                    </div>
                    <div class="absolute top-2 left-2">
                        <span class="badge bg-blue-500/80 text-white border-0 text-xs">Pre-Sale</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══ WHY CINEXPRESS ═══ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-10">
        <h2 class="section-title">Kenapa CineXpress?</h2>
        <p class="section-subtitle mt-2">Pengalaman menonton yang premium dari awal hingga akhir</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach([
            ['icon' => '⚡', 'title' => 'Booking Instan', 'desc' => 'Pilih kursi dan bayar dalam hitungan menit. Tanpa antri panjang.'],
            ['icon' => '💺', 'title' => 'Pilih Kursi Favorit', 'desc' => 'Interactive seat map memungkinkan kamu memilih kursi yang diinginkan.'],
            ['icon' => '🍿', 'title' => 'F&B Premium', 'desc' => 'Pesan makanan dan minuman favoritmu langsung saat booking tiket.'],
            ['icon' => '📱', 'title' => 'E-Ticket Digital', 'desc' => 'Tiket digital dengan QR Code. Langsung scan di pintu masuk bioskop.'],
        ] as $feature)
            <div class="card p-6 text-center group">
                <div class="text-4xl mb-4 group-hover:scale-110 transition-transform duration-300">{{ $feature['icon'] }}</div>
                <h3 class="font-display font-bold text-lg text-charcoal mb-2">{{ $feature['title'] }}</h3>
                <p class="text-charcoal-muted text-sm leading-relaxed">{{ $feature['desc'] }}</p>
            </div>
        @endforeach
    </div>
</section>

@endsection
