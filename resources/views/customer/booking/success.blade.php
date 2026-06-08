@extends('layouts.app')
@section('title', 'Booking Berhasil!')
@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    {{-- Success Animation --}}
    <div class="text-center mb-8 animate-bounce-in">
        <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
            <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h1 class="font-display text-3xl font-black text-charcoal mb-2">Pemesanan Berhasil! 🎉</h1>
        <p class="text-charcoal-muted">Tiket Anda telah diterbitkan. Nikmati filmnya!</p>
    </div>

    {{-- Transaction Card --}}
    <div class="eticket-card bg-white rounded-2xl shadow-espresso-lg overflow-hidden">
        {{-- Header --}}
        <div class="bg-espresso p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-white/60 uppercase tracking-wider">Kode Transaksi</p>
                    <p class="font-mono font-bold text-lg">{{ $transaction->transaction_code }}</p>
                </div>
                <span class="badge bg-green-400/20 text-green-300 border border-green-400/30">✓ Lunas</span>
            </div>
        </div>

        {{-- Movie Info --}}
        <div class="p-5 border-b border-dashed border-gray-200">
            <div class="flex gap-4">
                <div class="flex-1">
                    <h2 class="font-display font-bold text-xl text-charcoal">{{ $transaction->schedule->movie->title }}</h2>
                    <div class="flex flex-wrap gap-2 mt-2 text-sm text-charcoal-muted">
                        <span>📅 {{ $transaction->schedule->show_date->translatedFormat('l, d F Y') }}</span>
                        <span>🕐 {{ substr($transaction->schedule->start_time, 0, 5) }} WIB</span>
                        <span>🏛️ {{ $transaction->schedule->studio->name }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tickets --}}
        <div class="p-5">
            <p class="text-sm font-semibold text-charcoal-muted uppercase tracking-wider mb-3">Tiket Anda</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                @foreach($transaction->tickets as $ticket)
                    <div class="bg-primary/5 rounded-xl p-3 border border-primary/10 text-center">
                        <span class="text-2xl font-display font-black text-primary block">{{ $ticket->seat->seat_code }}</span>
                        <span class="text-xs text-charcoal-muted capitalize">{{ $ticket->seat_type }}</span>
                        <span class="block text-xs font-bold text-charcoal mt-1">{{ $ticket->price_formatted }}</span>
                        <span class="font-mono text-xs text-charcoal-muted">{{ $ticket->ticket_code }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        @if($transaction->fnbItems->count() > 0)
        <div class="px-5 pb-5 border-t border-gray-100 pt-4">
            <p class="text-sm font-semibold text-charcoal-muted uppercase tracking-wider mb-3">Pesanan F&B</p>
            <div class="space-y-2">
                @foreach($transaction->fnbItems as $item)
                    <div class="flex justify-between text-sm">
                        <span class="text-charcoal">{{ $item->fnbItem->name }} × {{ $item->quantity }}</span>
                        <span class="font-semibold">{{ $item->subtotal_formatted }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Total --}}
        <div class="bg-cream p-5 border-t border-dashed border-gray-200">
            <div class="space-y-1.5 text-sm">
                <div class="flex justify-between"><span class="text-charcoal-muted">Subtotal Tiket</span><span>Rp {{ number_format($transaction->subtotal_ticket, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-charcoal-muted">Subtotal F&B</span><span>Rp {{ number_format($transaction->subtotal_fnb, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-charcoal-muted">Pajak (10%)</span><span>Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</span></div>
                <div class="flex justify-between text-base font-bold border-t pt-2 mt-2">
                    <span>Total</span>
                    <span class="text-primary">{{ $transaction->total_formatted }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex flex-col sm:flex-row gap-3 mt-6">
        <a href="{{ route('my.eticket', $transaction->transaction_code) }}" class="btn-primary flex-1 py-3 text-base justify-center">
            🎟️ Lihat E-Ticket
        </a>
        <a href="{{ route('home') }}" class="btn-outline flex-1 py-3 text-base justify-center">
            Kembali ke Beranda
        </a>
    </div>
</div>
@endsection
