@extends('layouts.app')

@section('title', 'Riwayat Pemesanan')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="font-display text-2xl font-bold text-charcoal">Tiket & Riwayat</h1>
            <p class="text-charcoal-muted text-sm mt-0.5">Kelola semua pemesanan tiket Anda</p>
        </div>
        <a href="{{ route('movies.index') }}" class="btn-primary">
            🎬 Pesan Tiket Baru
        </a>
    </div>

    {{-- Filter Tabs --}}
    <div class="flex gap-2 mb-6 overflow-x-auto pb-1" x-data="{ active: '{{ request('status', 'all') }}' }">
        @foreach(['all' => 'Semua', 'paid' => '✓ Lunas', 'pending' => '⏳ Pending', 'failed' => '✗ Gagal'] as $val => $label)
            <a href="{{ route('my.transactions', ['status' => $val === 'all' ? null : $val]) }}"
               class="px-4 py-2 rounded-xl text-sm font-semibold whitespace-nowrap transition-all
                      {{ request('status', 'all') === $val || (request('status') === null && $val === 'all')
                          ? 'bg-primary text-white shadow-espresso'
                          : 'bg-white text-charcoal hover:bg-primary/10 shadow-card' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Transaction List --}}
    @forelse($transactions as $trx)
        <div class="card mb-4 p-5 group hover:border-primary/20 border border-transparent transition-all">
            <div class="flex items-start gap-4">
                {{-- Poster --}}
                <div class="w-16 flex-shrink-0 rounded-xl overflow-hidden bg-cream-dark" style="aspect-ratio: 2/3;">
                    @if($trx->schedule?->movie?->poster_image)
                        <img src="{{ $trx->schedule->movie->poster_url }}"
                             class="w-full h-full object-cover" alt="">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-2xl">🎬</div>
                    @endif
                </div>

                {{-- Details --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2 mb-1">
                        <h3 class="font-display font-bold text-charcoal line-clamp-1">
                            {{ $trx->schedule?->movie?->title ?? 'Film tidak tersedia' }}
                        </h3>
                        <span class="badge badge-{{ $trx->status_color }} flex-shrink-0">
                            {{ $trx->status_label }}
                        </span>
                    </div>

                    <div class="flex flex-wrap gap-3 mt-2 text-sm text-charcoal-muted">
                        @if($trx->schedule)
                            <span>📅 {{ $trx->schedule->show_date->translatedFormat('d F Y') }}</span>
                            <span>🕐 {{ substr($trx->schedule->start_time, 0, 5) }} WIB</span>
                            <span>🏛️ {{ $trx->schedule->studio->name ?? '-' }}</span>
                        @endif
                        <span>💺 {{ $trx->tickets->count() }} kursi</span>
                    </div>

                    {{-- Seats --}}
                    @if($trx->tickets->count() > 0)
                        <div class="flex flex-wrap gap-1 mt-2">
                            @foreach($trx->tickets as $ticket)
                                <span class="badge badge-primary text-xs">{{ $ticket->seat?->seat_code }}</span>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex items-center justify-between mt-3">
                        <div>
                            <span class="font-mono text-xs text-charcoal-muted">{{ $trx->transaction_code }}</span>
                            <p class="font-bold text-primary text-sm">{{ $trx->total_formatted }}</p>
                        </div>
                        <div class="flex gap-2">
                            @if($trx->payment_status === 'paid')
                                <a href="{{ route('my.eticket', $trx->transaction_code) }}"
                                   class="btn-primary btn-sm">
                                    🎟️ E-Ticket
                                </a>
                            @elseif($trx->payment_status === 'pending')
                                <span class="badge badge-yellow">Menunggu Bayar</span>
                            @endif
                            <a href="{{ route('my.transactions.show', $trx) }}" class="btn-secondary btn-sm">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-20">
            <div class="text-7xl mb-4">🎟️</div>
            <h3 class="text-2xl font-display font-bold text-charcoal mb-2">Belum Ada Pemesanan</h3>
            <p class="text-charcoal-muted mb-6">Yuk, mulai pesan tiket film favoritmu!</p>
            <a href="{{ route('movies.index') }}" class="btn-primary btn-lg">
                Lihat Film Sekarang
            </a>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if($transactions->hasPages())
        <div class="mt-6">{{ $transactions->links() }}</div>
    @endif
</div>
@endsection
