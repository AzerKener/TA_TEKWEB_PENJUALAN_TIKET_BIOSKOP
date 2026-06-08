@extends('layouts.app')
@section('title', 'Checkout — Konfirmasi Pemesanan')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Step Indicator --}}
    <div class="step-indicator max-w-xl mx-auto mb-10">
        @foreach(['Pilih Jadwal', 'Pilih Kursi', 'F&B', 'Checkout'] as $i => $step)
            <div class="step-item {{ $i < 3 ? 'completed' : 'active' }}">
                <div class="step-circle">
                    @if($i < 3)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    @else
                        {{ $i + 1 }}
                    @endif
                </div>
                <span class="text-xs mt-1.5 font-medium {{ $i === 3 ? 'text-primary font-bold' : 'text-green-600' }}">{{ $step }}</span>
            </div>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-3 gap-6" x-data="{ payMethod: 'transfer_bank' }">
        {{-- Order Detail --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Movie + Schedule --}}
            <div class="card p-5">
                <h2 class="section-title mb-4">Detail Pesanan</h2>
                <div class="flex gap-4">
                    <div class="w-16 flex-shrink-0 rounded-xl overflow-hidden bg-cream-dark" style="aspect-ratio: 2/3">
                        @if($schedule->movie->poster_image)
                            <img src="{{ $schedule->movie->poster_url }}" class="w-full h-full object-cover" alt="">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-2xl">🎬</div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h3 class="font-display font-bold text-lg text-charcoal">{{ $schedule->movie->title }}</h3>
                        <div class="mt-2 space-y-1 text-sm text-charcoal-muted">
                            <p>📅 {{ $schedule->show_date->translatedFormat('l, d F Y') }}</p>
                            <p>🕐 {{ substr($schedule->start_time, 0, 5) }} — {{ substr($schedule->end_time, 0, 5) }} WIB</p>
                            <p>🏛️ {{ $schedule->studio->name }} ({{ $schedule->studio->type_label }})</p>
                            <p>🎭 {{ $schedule->language_type_label }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Seats --}}
            <div class="card p-5">
                <h3 class="font-semibold text-charcoal mb-3">
                    Kursi Terpilih
                    <span class="text-sm font-normal text-charcoal-muted">({{ $lockedSeats->count() }} kursi)</span>
                </h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($lockedSeats as $lock)
                        @php
                            $seatPrice = match($lock->seat->type) {
                                'vip' => $schedule->price_vip,
                                'couple' => $schedule->price_couple,
                                default => $schedule->price_regular,
                            };
                        @endphp
                        <div class="flex flex-col items-center px-3 py-2 bg-primary/5 rounded-xl border border-primary/15">
                            <span class="font-display font-black text-xl text-primary">{{ $lock->seat->seat_code }}</span>
                            <span class="text-xs text-charcoal-muted capitalize">{{ $lock->seat->type }}</span>
                            <span class="text-xs font-bold text-charcoal">
                                Rp {{ number_format($seatPrice, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- F&B Items if any --}}
            @if(count($fnbItems) > 0)
                <div class="card p-5">
                    <h3 class="font-semibold text-charcoal mb-3">Makanan & Minuman</h3>
                    <div class="space-y-2">
                        @foreach($fnbItems as $fnb)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-charcoal">{{ $fnb['item']->name }} × {{ $fnb['quantity'] }}</span>
                                <span class="font-semibold text-charcoal">Rp {{ number_format($fnb['subtotal'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Payment Method --}}
            <div class="card p-5">
                <h3 class="font-semibold text-charcoal mb-4">Metode Pembayaran</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach([
                        ['transfer_bank', '🏦', 'Transfer Bank'],
                        ['gopay', '💚', 'GoPay'],
                        ['ovo', '💜', 'OVO'],
                        ['dana', '💙', 'DANA'],
                        ['shopee_pay', '🧡', 'ShopeePay'],
                        ['qris', '📱', 'QRIS'],
                        ['credit_card', '💳', 'Kartu Kredit'],
                        ['debit_card', '🪙', 'Kartu Debit'],
                    ] as [$val, $icon, $label])
                        <label class="cursor-pointer" @click="payMethod = '{{ $val }}'">
                            <div :class="payMethod === '{{ $val }}' ? 'border-primary bg-primary/5 shadow-espresso' : 'border-gray-200 hover:border-primary/40'"
                                 class="border-2 rounded-xl p-3 text-center transition-all select-none">
                                <div class="text-2xl mb-1">{{ $icon }}</div>
                                <p class="text-xs font-semibold text-charcoal leading-tight">{{ $label }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Summary Sidebar --}}
        <div>
            <div class="card p-5 sticky top-20">
                <h3 class="font-display font-bold text-charcoal mb-4">Ringkasan Pembayaran</h3>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-charcoal-muted">Tiket ({{ $lockedSeats->count() }}x)</span>
                        <span>Rp {{ number_format($subtotalTicket, 0, ',', '.') }}</span>
                    </div>
                    @if($subtotalFnb > 0)
                        <div class="flex justify-between">
                            <span class="text-charcoal-muted">F&B</span>
                            <span>Rp {{ number_format($subtotalFnb, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-charcoal-muted">Pajak (10%)</span>
                        <span>Rp {{ number_format($taxAmount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-base pt-3 border-t border-dashed">
                        <span>Total</span>
                        <span class="text-primary">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Checkout Form --}}
                <form action="{{ route('booking.process') }}" method="POST" class="mt-5">
                    @csrf
                    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                    @foreach($lockedSeats as $lock)
                        <input type="hidden" name="seat_ids[]" value="{{ $lock->seat_id }}">
                    @endforeach
                    @foreach($fnbCart as $fnbId => $qty)
                        <input type="hidden" name="fnb[{{ $fnbId }}]" value="{{ $qty }}">
                    @endforeach
                    <input type="hidden" name="payment_method" :value="payMethod">

                    <div class="bg-primary/5 rounded-xl p-3 mb-4 text-xs text-charcoal-muted border border-primary/10">
                        <p class="font-semibold text-charcoal mb-1">⚠️ Penting:</p>
                        <ul class="space-y-0.5 list-disc pl-3">
                            <li>Kursi dikonfirmasi setelah pembayaran</li>
                            <li>Pesanan kedaluwarsa dalam 15 menit</li>
                            <li>Tiket tidak dapat direfund</li>
                        </ul>
                    </div>

                    <button type="submit" class="btn-gold w-full py-3.5 text-base justify-center font-bold">
                        💳 Bayar Sekarang
                    </button>
                </form>

                <a href="{{ route('booking.seats', $schedule) }}"
                   class="btn-ghost w-full mt-2 text-sm justify-center">
                    ← Ubah Kursi
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
