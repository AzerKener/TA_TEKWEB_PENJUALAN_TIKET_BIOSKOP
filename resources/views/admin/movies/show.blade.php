@extends('layouts.admin')
@section('title', 'Detail Film — ' . $movie->title)

@section('content')
<div class="max-w-4xl">
    <div class="flex items-center gap-3 mb-5">
        <a href="{{ route('admin.movies.index') }}" class="btn-secondary btn-sm">← Kembali</a>
        <a href="{{ route('admin.movies.edit', $movie) }}" class="btn-primary btn-sm">Edit Film</a>
    </div>

    <div class="grid md:grid-cols-3 gap-5">
        {{-- Poster --}}
        <div>
            <div class="aspect-[2/3] rounded-2xl overflow-hidden shadow-card bg-cream-dark">
                @if($movie->poster_image)
                    <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-6xl">🎬</div>
                @endif
            </div>
        </div>

        {{-- Info --}}
        <div class="md:col-span-2 space-y-4">
            <div class="bg-white rounded-2xl p-5 shadow-card">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <h1 class="font-display text-2xl font-bold text-charcoal">{{ $movie->title }}</h1>
                    <span class="badge badge-{{ $movie->status_color }} flex-shrink-0">{{ $movie->status_label }}</span>
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    @foreach([
                        ['Sutradara', $movie->director],
                        ['Durasi', $movie->duration_formatted],
                        ['Rating', $movie->rating],
                        ['Bahasa', $movie->language],
                        ['Rilis', $movie->release_date->format('d M Y')],
                        ['IMDb', $movie->imdb_rating ? $movie->imdb_rating . '/10' : '-'],
                        ['Genre', $movie->genre_list ?: '-'],
                        ['Produksi', $movie->production_company ?? '-'],
                    ] as [$key, $val])
                        <div>
                            <p class="text-xs text-charcoal-muted">{{ $key }}</p>
                            <p class="font-semibold text-charcoal">{{ $val }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-4 border-t">
                    <p class="text-xs text-charcoal-muted mb-1">Sinopsis</p>
                    <p class="text-sm text-charcoal leading-relaxed">{{ $movie->synopsis }}</p>
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-white rounded-xl p-4 shadow-card text-center">
                    <p class="text-2xl font-display font-black text-primary">{{ $movie->schedules->count() }}</p>
                    <p class="text-xs text-charcoal-muted">Jadwal</p>
                </div>
                <div class="bg-white rounded-xl p-4 shadow-card text-center">
                    <p class="text-2xl font-display font-black text-blue-600">{{ $movie->reviews_count }}</p>
                    <p class="text-xs text-charcoal-muted">Ulasan</p>
                </div>
                <div class="bg-white rounded-xl p-4 shadow-card text-center">
                    <p class="text-2xl font-display font-black text-yellow-600">{{ $movie->average_rating }}</p>
                    <p class="text-xs text-charcoal-muted">Avg Rating</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Schedules --}}
    <div class="bg-white rounded-2xl shadow-card overflow-hidden mt-5">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-display font-bold text-charcoal">Jadwal Tayang</h3>
            <a href="{{ route('admin.schedules.create') }}" class="btn-primary btn-sm">+ Tambah Jadwal</a>
        </div>
        <div class="overflow-x-auto">
            <table class="table-admin">
                <thead><tr><th>Studio</th><th>Tanggal</th><th>Jam</th><th>Harga</th><th>Tiket Terjual</th></tr></thead>
                <tbody>
                    @forelse($movie->schedules->sortByDesc('show_date')->take(10) as $schedule)
                        <tr>
                            <td>{{ $schedule->studio->name }}</td>
                            <td>{{ $schedule->show_date->format('d/m/Y') }}</td>
                            <td class="font-mono text-sm">{{ substr($schedule->start_time, 0, 5) }}</td>
                            <td>Rp {{ number_format($schedule->price_regular, 0, ',', '.') }}</td>
                            <td>{{ $schedule->tickets->count() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-6 text-gray-400">Belum ada jadwal</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
