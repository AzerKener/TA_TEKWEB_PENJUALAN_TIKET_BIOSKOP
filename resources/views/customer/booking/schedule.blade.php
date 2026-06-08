@extends('layouts.app')
@section('title', 'Pilih Jadwal — ' . $movie->title)
@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Step Indicator --}}
    <div class="step-indicator max-w-xl mx-auto mb-10">
        @foreach(['Pilih Jadwal', 'Pilih Kursi', 'F&B', 'Checkout'] as $i => $step)
            <div class="step-item {{ $i === 0 ? 'active' : '' }}">
                <div class="step-circle">{{ $i + 1 }}</div>
                <span class="text-xs mt-1.5 font-medium {{ $i === 0 ? 'text-primary font-bold' : 'text-charcoal-muted' }}">{{ $step }}</span>
            </div>
        @endforeach
    </div>

    {{-- Movie Header --}}
    <div class="flex items-start gap-5 mb-8 glass rounded-2xl p-5">
        <div class="w-20 h-28 rounded-xl overflow-hidden bg-cream-dark flex-shrink-0">
            @if($movie->poster_image)
                <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-gradient-to-br from-primary/20 to-primary/40 flex items-center justify-center">
                    <svg class="w-8 h-8 text-primary/50" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/></svg>
                </div>
            @endif
        </div>
        <div>
            <h1 class="font-display text-2xl font-bold text-charcoal">{{ $movie->title }}</h1>
            <div class="flex flex-wrap gap-2 mt-2">
                <span class="badge badge-primary">{{ $movie->rating }}</span>
                <span class="badge badge-gray">{{ $movie->duration_formatted }}</span>
                <span class="badge badge-gray">{{ $movie->language }}</span>
                @foreach(array_slice($movie->genre ?? [], 0, 3) as $genre)
                    <span class="badge badge-blue">{{ $genre }}</span>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Schedule by Date --}}
    @forelse($schedules as $dateStr => $daySchedules)
        @php $date = \Carbon\Carbon::parse($dateStr); @endphp
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="text-center bg-primary text-white rounded-xl px-3 py-2 min-w-[60px]">
                    <p class="text-2xl font-display font-black leading-none">{{ $date->format('d') }}</p>
                    <p class="text-xs uppercase tracking-wide">{{ $date->translatedFormat('M') }}</p>
                </div>
                <div>
                    <p class="font-bold text-charcoal">{{ $date->translatedFormat('l') }}</p>
                    <p class="text-sm text-charcoal-muted">{{ $daySchedules->count() }} jadwal tersedia</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($daySchedules as $schedule)
                    <a href="{{ route('booking.seats', $schedule) }}"
                       class="card p-4 block group hover:border-primary/30 hover:bg-primary/5 border border-transparent transition-all">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="font-display font-black text-2xl text-primary">{{ substr($schedule->start_time, 0, 5) }}</span>
                                    <span class="text-charcoal-muted text-sm">— {{ substr($schedule->end_time, 0, 5) }}</span>
                                </div>
                                <p class="font-semibold text-charcoal">{{ $schedule->studio->name }}</p>
                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    <span class="badge badge-primary text-xs">{{ $schedule->studio->type_label }}</span>
                                    <span class="badge badge-blue text-xs">{{ $schedule->language_type_label }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-primary">Rp {{ number_format($schedule->price_regular, 0, ',', '.') }}</p>
                                <p class="text-xs text-charcoal-muted">/ kursi regular</p>
                                <p class="text-xs text-{{ $schedule->available_seats_count < 10 ? 'red' : 'green' }}-600 font-semibold mt-2">
                                    {{ $schedule->available_seats_count }} kursi tersisa
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @empty
        <div class="text-center py-16">
            <div class="text-6xl mb-4">🎬</div>
            <h3 class="text-xl font-bold text-charcoal mb-2">Tidak Ada Jadwal Tersedia</h3>
            <p class="text-charcoal-muted mb-6">Belum ada jadwal tayang yang tersedia untuk film ini.</p>
            <a href="{{ route('movies.index') }}" class="btn-primary">Lihat Film Lainnya</a>
        </div>
    @endforelse
</div>
@endsection
