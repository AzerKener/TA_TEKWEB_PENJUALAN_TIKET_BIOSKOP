@extends('layouts.app')

@section('title', 'Detail Transaksi — ' . $transaction->transaction_code)

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('my.transactions') }}" class="btn-ghost p-2 rounded-xl">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h1 class="font-display text-xl font-bold text-charcoal">Detail Pemesanan</h1>
    </div>

    <div class="card overflow-hidden">
        {{-- Header --}}
        <div class="bg-espresso p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-white/60 uppercase tracking-wider mb-1">Kode Transaksi</p>
                    <p class="font-mono font-bold text-lg">{{ $transaction->transaction_code }}</p>
                </div>
                <span class="badge badge-{{ $transaction->status_color }} text-sm">{{ $transaction->status_label }}</span>
            </div>
        </div>

        {{-- Film --}}
        <div class="p-5 border-b border-dashed border-gray-200">
            <h2 class="font-display font-bold text-xl text-charcoal mb-2">
                {{ $transaction->schedule->movie->title }}
            </h2>
            <div class="flex flex-col gap-1.5 text-sm text-charcoal-muted">
                <span>📅 {{ $transaction->schedule->show_date->translatedFormat('l, d F Y') }}</span>
                <span>🕐 {{ substr($transaction->schedule->start_time, 0, 5) }} — {{ substr($transaction->schedule->end_time, 0, 5) }} WIB</span>
                <span>🏛️ {{ $transaction->schedule->studio->name }}</span>
            </div>
        </div>

        {{-- Tickets --}}
        <div class="p-5 border-b border-dashed border-gray-200">
            <p class="text-sm font-semibold text-charcoal-muted uppercase tracking-wider mb-3">Kursi</p>
            <div class="flex flex-wrap gap-2">
                @foreach($transaction->tickets as $ticket)
                    <div class="bg-primary/5 rounded-xl p-3 border border-primary/10 text-center min-w-[70px]">
                        <span class="text-xl font-display font-black text-primary block">{{ $ticket->seat->seat_code }}</span>
                        <span class="text-xs text-charcoal-muted capitalize">{{ $ticket->seat_type }}</span>
                        <span class="block text-xs font-bold text-charcoal mt-0.5">
                            Rp {{ number_format($ticket->price, 0, ',', '.') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- F&B --}}
        @if($transaction->fnbItems->count() > 0)
            <div class="p-5 border-b border-dashed border-gray-200">
                <p class="text-sm font-semibold text-charcoal-muted uppercase tracking-wider mb-3">Makanan & Minuman</p>
                <div class="space-y-2">
                    @foreach($transaction->fnbItems as $item)
                        <div class="flex justify-between text-sm">
                            <span class="text-charcoal">{{ $item->fnbItem->name }} × {{ $item->quantity }}</span>
                            <span class="font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Payment Summary --}}
        <div class="p-5 bg-cream/50">
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-charcoal-muted">Subtotal Tiket</span>
                    <span>Rp {{ number_format($transaction->subtotal_ticket, 0, ',', '.') }}</span>
                </div>
                @if($transaction->subtotal_fnb > 0)
                    <div class="flex justify-between">
                        <span class="text-charcoal-muted">Subtotal F&B</span>
                        <span>Rp {{ number_format($transaction->subtotal_fnb, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-charcoal-muted">Pajak (10%)</span>
                    <span>Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold text-base pt-2 border-t">
                    <span>Total</span>
                    <span class="text-primary">{{ $transaction->total_formatted }}</span>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-dashed text-xs text-charcoal-muted space-y-1">
                <p>Metode: <span class="font-semibold text-charcoal">{{ $transaction->payment_method_label }}</span></p>
                @if($transaction->paid_at)
                    <p>Dibayar: <span class="font-semibold text-charcoal">{{ $transaction->paid_at->translatedFormat('d F Y H:i') }} WIB</span></p>
                @endif
            </div>
        </div>
    </div>

    @if($transaction->payment_status === 'paid')
        <div class="flex gap-3 mt-5">
            <a href="{{ route('my.eticket', $transaction->transaction_code) }}"
               class="btn-primary flex-1 py-3 justify-center text-base">
                🎟️ Lihat E-Ticket
            </a>
        </div>
    @endif
</div>
@endsection
