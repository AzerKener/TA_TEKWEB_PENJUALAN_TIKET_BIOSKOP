@extends('layouts.app')

@section('title', 'Daftar Film')
@section('meta_description', 'Temukan film terbaru yang sedang tayang dan segera tayang di CineXpress.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="section-title">Daftar Film</h1>
            <p class="section-subtitle mt-1">Temukan film favoritmu</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('movies.index') }}" class="glass rounded-2xl p-4 mb-8">
        <div class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="form-label text-xs">Cari Film</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Judul film..." class="form-input max-w-xs">
            </div>
            <div>
                <label class="form-label text-xs">Status</label>
                <select name="status" class="form-input max-w-xs">
                    <option value="">Semua Status</option>
                    <option value="now_playing" {{ request('status') === 'now_playing' ? 'selected' : '' }}>🎬 Sedang Tayang</option>
                    <option value="coming_soon" {{ request('status') === 'coming_soon' ? 'selected' : '' }}>🕐 Segera Tayang</option>
                </select>
            </div>
            <div>
                <label class="form-label text-xs">Genre</label>
                <select name="genre" class="form-input max-w-xs">
                    <option value="">Semua Genre</option>
                    @foreach($allGenres as $genre)
                        <option value="{{ $genre }}" {{ request('genre') === $genre ? 'selected' : '' }}>{{ $genre }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'genre']))
                <a href="{{ route('movies.index') }}" class="btn-ghost">Reset</a>
            @endif
        </div>
    </form>

    {{-- Status Tabs --}}
    <div class="flex gap-2 mb-6 overflow-x-auto pb-1">
        @foreach(['' => 'Semua', 'now_playing' => '🎬 Sedang Tayang', 'coming_soon' => '🕐 Segera Tayang'] as $val => $label)
            <a href="{{ route('movies.index', array_merge(request()->except('status', 'page'), $val ? ['status' => $val] : [])) }}"
               class="px-4 py-2 rounded-xl text-sm font-semibold whitespace-nowrap transition-all
                      {{ request('status', '') === $val ? 'bg-primary text-white shadow-espresso' : 'bg-white text-charcoal hover:bg-primary/10 shadow-card' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Movies Grid --}}
    @if($movies->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            @foreach($movies as $movie)
                <div class="card-movie group" onclick="window.location='{{ route('movies.show', $movie->slug) }}'">
                    {{-- Poster --}}
                    <div class="relative aspect-[2/3] overflow-hidden bg-cream-dark">
                        @if($movie->poster_image)
                            <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" class="movie-poster">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-primary/20 to-primary/40 flex items-center justify-center">
                                <svg class="w-10 h-10 text-primary/40" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/></svg>
                            </div>
                        @endif

                        {{-- Hover overlay --}}
                        <div class="absolute inset-0 poster-overlay opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-3">
                            @if($movie->status === 'now_playing')
                                <a href="{{ route('booking.schedule', $movie) }}"
                                   class="btn-gold w-full text-xs py-2 justify-center"
                                   onclick="event.stopPropagation()">
                                    🎟️ Beli Tiket
                                </a>
                            @else
                                <div class="w-full py-2 text-center text-xs text-white/80 bg-black/40 rounded-xl">
                                    Segera Tayang
                                </div>
                            @endif
                        </div>

                        {{-- Badges --}}
                        <div class="absolute top-2 left-2">
                            <span class="badge border-0 text-xs
                                {{ $movie->status === 'now_playing' ? 'bg-green-500/85 text-white' : 'bg-blue-500/85 text-white' }}">
                                {{ $movie->status_label }}
                            </span>
                        </div>
                        @if($movie->imdb_rating)
                            <div class="absolute top-2 right-2">
                                <span class="badge bg-yellow-500/90 text-charcoal border-0 text-xs font-bold">⭐ {{ $movie->imdb_rating }}</span>
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
                            <span class="badge-gray badge text-xs">{{ $movie->rating }}</span>
                        </div>
                        @if(count($movie->genre ?? []) > 0)
                            <div class="flex flex-wrap gap-1 mt-1.5">
                                @foreach(array_slice($movie->genre, 0, 2) as $genre)
                                    <span class="badge-primary badge text-xs">{{ $genre }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $movies->links() }}
        </div>
    @else
        <div class="text-center py-20">
            <div class="text-7xl mb-4">🎭</div>
            <h3 class="text-2xl font-display font-bold text-charcoal mb-2">Film Tidak Ditemukan</h3>
            <p class="text-charcoal-muted mb-6">Coba ubah kata kunci atau filter pencarian Anda.</p>
            <a href="{{ route('movies.index') }}" class="btn-primary">Lihat Semua Film</a>
        </div>
    @endif
</div>
@endsection
