@extends('layouts.app')

@section('title', $movie->title)
@section('meta_description', Str::limit($movie->synopsis, 160))

@section('content')

{{-- Hero Banner --}}
<div class="relative bg-charcoal overflow-hidden">
    @if($movie->poster_image)
        <div class="absolute inset-0">
            <img src="{{ $movie->poster_url }}" alt="" class="w-full h-full object-cover opacity-15 blur-sm scale-110">
            <div class="absolute inset-0 bg-gradient-to-r from-charcoal via-charcoal/90 to-charcoal/60"></div>
        </div>
    @endif

    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <nav class="flex items-center gap-2 text-xs text-white/50 mb-6">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a>
            <span>/</span>
            <a href="{{ route('movies.index') }}" class="hover:text-white transition-colors">Film</a>
            <span>/</span>
            <span class="text-white/80">{{ $movie->title }}</span>
        </nav>

        <div class="flex flex-col md:flex-row gap-8 items-start">
            {{-- Poster --}}
            <div class="flex-shrink-0">
                <div class="w-44 md:w-52 rounded-2xl overflow-hidden shadow-2xl ring-2 ring-white/10">
                    @if($movie->poster_image)
                        <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" class="w-full h-auto">
                    @else
                        <div class="aspect-[2/3] bg-gradient-to-br from-primary/40 to-primary/20 flex items-center justify-center">
                            <svg class="w-16 h-16 text-white/30" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/></svg>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Info --}}
            <div class="flex-1 text-white">
                <div class="flex flex-wrap gap-2 mb-3">
                    <span class="badge border-0 text-xs {{ $movie->status === 'now_playing' ? 'bg-green-500/80 text-white' : 'bg-blue-500/80 text-white' }}">
                        {{ $movie->status_label }}
                    </span>
                    <span class="badge bg-white/10 text-white border-white/20 text-xs">{{ $movie->rating }}</span>
                    @foreach(array_slice($movie->genre ?? [], 0, 3) as $genre)
                        <span class="badge bg-primary/40 text-white border-primary/30 text-xs">{{ $genre }}</span>
                    @endforeach
                </div>

                <h1 class="font-display text-3xl md:text-4xl font-black leading-tight mb-4">{{ $movie->title }}</h1>

                {{-- Meta stats --}}
                <div class="flex flex-wrap items-center gap-4 mb-4 text-sm text-white/70">
                    <span class="flex items-center gap-1">⏱️ {{ $movie->duration_formatted }}</span>
                    <span>🌐 {{ $movie->language }}</span>
                    @if($movie->imdb_rating)
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            {{ $movie->imdb_rating }}/10 IMDb
                        </span>
                    @endif
                    @if($movie->average_rating > 0)
                        <span>⭐ {{ $movie->average_rating }}/5 ({{ $movie->reviews_count }} ulasan)</span>
                    @endif
                </div>

                <p class="text-white/70 leading-relaxed mb-6 max-w-2xl">{{ $movie->synopsis }}</p>

                <div class="flex flex-wrap gap-3">
                    @if($movie->status === 'now_playing')
                        <a href="{{ route('booking.schedule', $movie) }}" class="btn-gold btn-lg">
                            🎟️ Beli Tiket
                        </a>
                    @endif
                    @if($movie->trailer_url)
                        <a href="{{ $movie->trailer_url }}" target="_blank" rel="noopener"
                           class="btn border-2 border-white/20 text-white hover:bg-white/10 btn-lg">
                            ▶ Tonton Trailer
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Main Content --}}
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid lg:grid-cols-3 gap-8">

        {{-- LEFT: Main Details --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- Details Grid --}}
            <div class="card p-5">
                <h2 class="font-display font-bold text-xl text-charcoal mb-4">Detail Film</h2>
                <dl class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @foreach([
                        ['Sutradara', $movie->director],
                        ['Durasi', $movie->duration_formatted],
                        ['Bahasa', $movie->language],
                        ['Rating', $movie->rating],
                        ['Rilis', $movie->release_date->format('d M Y')],
                        ['Produksi', $movie->production_company ?? '-'],
                    ] as [$label, $value])
                        <div class="bg-cream rounded-xl p-3">
                            <dt class="text-xs text-charcoal-muted mb-0.5">{{ $label }}</dt>
                            <dd class="font-semibold text-charcoal text-sm">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>

                @if($movie->cast)
                    <div class="mt-4 pt-4 border-t border-black/5">
                        <dt class="text-xs text-charcoal-muted mb-1">Pemeran</dt>
                        <dd class="text-sm text-charcoal">{{ $movie->cast }}</dd>
                    </div>
                @endif
            </div>

            {{-- Trailer --}}
            @if($movie->youtube_embed_url)
                <div class="card overflow-hidden">
                    <div class="p-5 pb-3">
                        <h2 class="font-display font-bold text-xl text-charcoal">Trailer Resmi</h2>
                    </div>
                    <div class="aspect-video">
                        <iframe src="{{ $movie->youtube_embed_url }}"
                                class="w-full h-full" allowfullscreen loading="lazy"
                                title="Trailer {{ $movie->title }}"></iframe>
                    </div>
                </div>
            @endif

            {{-- Schedules Preview --}}
            @if($schedulesByDate->count() > 0)
                <div class="card p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-display font-bold text-xl text-charcoal">Jadwal Tayang</h2>
                        <a href="{{ route('booking.schedule', $movie) }}" class="text-sm text-primary font-semibold hover:underline">
                            Lihat semua →
                        </a>
                    </div>

                    @foreach($schedulesByDate->take(3) as $dateStr => $daySchedules)
                        @php $date = \Carbon\Carbon::parse($dateStr); @endphp
                        <div class="mb-4">
                            <p class="text-sm font-semibold text-charcoal-muted mb-2 flex items-center gap-1.5">
                                📅 {{ $date->translatedFormat('l, d F Y') }}
                            </p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($daySchedules as $schedule)
                                    <a href="{{ route('booking.seats', $schedule) }}"
                                       class="flex flex-col items-center px-3 py-2 bg-cream border border-primary/15
                                              rounded-xl hover:bg-primary hover:text-white hover:border-primary
                                              transition-all group text-center">
                                        <span class="font-bold text-sm">{{ substr($schedule->start_time, 0, 5) }}</span>
                                        <span class="text-xs opacity-70 group-hover:opacity-100">{{ $schedule->studio->name }}</span>
                                        <span class="text-xs font-semibold text-primary group-hover:text-white mt-0.5">
                                            Rp {{ number_format($schedule->price_regular, 0, ',', '.') }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Reviews --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-display font-bold text-xl text-charcoal">
                        Ulasan Penonton
                        <span class="text-base font-normal text-charcoal-muted">({{ $movie->reviews_count }})</span>
                    </h2>
                    @if($movie->average_rating > 0)
                        <div class="flex items-center gap-2">
                            <span class="text-2xl font-bold text-charcoal">{{ $movie->average_rating }}</span>
                            <div>
                                <div class="text-yellow-400 text-sm">{{ str_repeat('★', round($movie->average_rating)) }}{{ str_repeat('☆', 5 - round($movie->average_rating)) }}</div>
                                <div class="text-xs text-charcoal-muted">dari 5</div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Write Review Form --}}
                @auth
                    @if(!$userHasReviewed)
                        <div class="glass rounded-2xl p-5 mb-6" x-data="{ rating: 0, hover: 0 }">
                            <h3 class="font-semibold text-charcoal mb-3">Tulis Ulasan Anda</h3>
                            <form action="{{ route('movies.review', $movie) }}" method="POST">
                                @csrf
                                <div class="flex gap-1 mb-4">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button"
                                                @click="rating = {{ $i }}"
                                                @mouseover="hover = {{ $i }}"
                                                @mouseleave="hover = 0"
                                                class="text-3xl transition-transform hover:scale-110">
                                            <span :class="(hover || rating) >= {{ $i }} ? 'text-yellow-400' : 'text-gray-200'">★</span>
                                        </button>
                                    @endfor
                                    <input type="hidden" name="rating" :value="rating">
                                </div>
                                <textarea name="comment" rows="3"
                                          class="form-input mb-3"
                                          placeholder="Bagikan pengalaman menonton Anda..."></textarea>
                                <button type="submit" class="btn-primary" :disabled="rating === 0"
                                        :class="rating === 0 && 'opacity-50 cursor-not-allowed'">
                                    Kirim Ulasan
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="bg-green-50 rounded-2xl p-4 mb-6 border border-green-200">
                            <p class="text-green-700 text-sm font-medium">✓ Anda sudah memberikan ulasan untuk film ini.</p>
                        </div>
                    @endif
                @else
                    <div class="glass rounded-2xl p-4 mb-6 text-center">
                        <p class="text-charcoal-muted text-sm">
                            <a href="{{ route('login') }}" class="text-primary font-semibold hover:underline">Masuk</a>
                            untuk memberikan ulasan
                        </p>
                    </div>
                @endauth

                {{-- Reviews List --}}
                <div class="space-y-4">
                    @forelse($movie->reviews as $review)
                        <div class="card p-4 animate-fade-in">
                            <div class="flex items-start gap-3">
                                <img src="{{ $review->user->avatar_url }}"
                                     alt="{{ $review->user->name }}"
                                     class="w-9 h-9 rounded-xl flex-shrink-0 object-cover">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="font-semibold text-charcoal text-sm">{{ $review->user->name }}</p>
                                        <span class="text-yellow-400 text-sm flex-shrink-0">{{ $review->stars }}</span>
                                    </div>
                                    @if($review->comment)
                                        <p class="text-charcoal-muted text-sm mt-1.5 leading-relaxed">{{ $review->comment }}</p>
                                    @endif
                                    <p class="text-xs text-charcoal-muted mt-2">{{ $review->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <div class="text-4xl mb-2">💬</div>
                            <p class="text-charcoal-muted">Belum ada ulasan. Jadilah yang pertama!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- RIGHT: Sidebar --}}
        <div class="space-y-5">
            {{-- Quick Action --}}
            @if($movie->status === 'now_playing')
                <div class="card p-5 text-center">
                    <div class="text-4xl mb-3">🎬</div>
                    <h3 class="font-display font-bold text-lg text-charcoal mb-2">Siap Menonton?</h3>
                    <p class="text-charcoal-muted text-sm mb-4">Pesan tiket sekarang sebelum kursi habis!</p>
                    <a href="{{ route('booking.schedule', $movie) }}" class="btn-primary w-full py-3 justify-center">
                        🎟️ Pesan Tiket
                    </a>
                </div>
            @endif

            {{-- Movie Info Card --}}
            <div class="card p-5">
                <h3 class="font-display font-bold text-charcoal mb-4">Info Film</h3>
                <div class="space-y-3 text-sm">
                    @if($movie->production_company)
                        <div class="flex justify-between gap-2">
                            <span class="text-charcoal-muted">Produksi</span>
                            <span class="font-medium text-right">{{ $movie->production_company }}</span>
                        </div>
                    @endif
                    @if($movie->distributor)
                        <div class="flex justify-between gap-2">
                            <span class="text-charcoal-muted">Distributor</span>
                            <span class="font-medium text-right">{{ $movie->distributor }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between gap-2">
                        <span class="text-charcoal-muted">Subtitle</span>
                        <span class="font-medium">{{ $movie->has_subtitle ? '✓ Tersedia' : '✗ Tidak ada' }}</span>
                    </div>
                </div>
            </div>

            {{-- Related --}}
            @if($relatedMovies->count() > 0)
                <div class="card p-4">
                    <h3 class="font-display font-bold text-charcoal mb-3 text-sm uppercase tracking-wider">Film Serupa</h3>
                    <div class="space-y-3">
                        @foreach($relatedMovies as $rel)
                            <a href="{{ route('movies.show', $rel->slug) }}" class="flex gap-3 group">
                                <div class="w-12 h-16 flex-shrink-0 rounded-lg overflow-hidden bg-cream-dark">
                                    @if($rel->poster_image)
                                        <img src="{{ $rel->poster_url }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform" alt="">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-xl">🎬</div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-charcoal text-sm line-clamp-2 group-hover:text-primary transition-colors">{{ $rel->title }}</p>
                                    <p class="text-xs text-charcoal-muted mt-0.5">{{ $rel->duration_formatted }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
