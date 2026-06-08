@extends('layouts.app')
@section('title', 'Pilih F&B')
@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="fnbCart({{ $schedule->id }})">

    {{-- Step Indicator --}}
    <div class="step-indicator max-w-xl mx-auto mb-10">
        @foreach(['Pilih Jadwal', 'Pilih Kursi', 'F&B', 'Checkout'] as $i => $step)
            <div class="step-item {{ $i < 2 ? 'completed' : ($i === 2 ? 'active' : '') }}">
                <div class="step-circle">
                    @if($i < 2)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    @else
                        {{ $i + 1 }}
                    @endif
                </div>
                <span class="text-xs mt-1.5 font-medium {{ $i === 2 ? 'text-primary font-bold' : ($i < 2 ? 'text-green-600' : 'text-charcoal-muted') }}">{{ $step }}</span>
            </div>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- FNB Items --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="section-title">Tambah Makanan & Minuman</h2>
                <span class="text-sm text-charcoal-muted">(Opsional)</span>
            </div>

            @foreach($fnbItems as $category => $items)
                <div>
                    <h3 class="font-display font-bold text-lg text-charcoal mb-3 capitalize flex items-center gap-2">
                        {{ $category === 'food' ? '🍔' : ($category === 'drink' ? '🥤' : ($category === 'combo' ? '🎁' : '🍟')) }}
                        {{ $items->first()->category_label }}
                    </h3>
                    <div class="grid sm:grid-cols-2 gap-3">
                        @foreach($items as $item)
                            <div class="card p-4 flex items-center gap-3 group">
                                <div class="w-16 h-16 rounded-xl bg-cream flex-shrink-0 overflow-hidden">
                                    @if($item->image)
                                        <img src="{{ $item->image_url }}" class="w-full h-full object-cover" alt="{{ $item->name }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-2xl">{{ $category === 'drink' ? '🥤' : '🍿' }}</div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-charcoal text-sm">{{ $item->name }}</p>
                                    <p class="text-primary font-bold text-sm">{{ $item->price_formatted }}</p>
                                </div>
                                {{-- Quantity Control --}}
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <button @click="removeItem({{ $item->id }})" class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-red-100 hover:text-red-600 flex items-center justify-center transition-colors text-sm font-bold" :class="getQty({{ $item->id }}) === 0 && 'opacity-30 cursor-not-allowed'" :disabled="getQty({{ $item->id }}) === 0">−</button>
                                    <span class="w-6 text-center font-bold text-charcoal text-sm" x-text="getQty({{ $item->id }})">0</span>
                                    <button @click="addItem({{ $item->id }}, {{ $item->price }})" class="w-7 h-7 rounded-lg bg-primary/10 hover:bg-primary hover:text-white text-primary flex items-center justify-center transition-colors text-sm font-bold">+</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Summary --}}
        <div>
            <div class="bg-white rounded-2xl p-5 shadow-card sticky top-20">
                <h3 class="font-display font-bold text-charcoal mb-4">Ringkasan F&B</h3>

                <div class="space-y-2 min-h-[60px]">
                    <template x-for="(data, id) in cart" :key="id">
                        <div class="flex justify-between text-sm">
                            <span class="text-charcoal-muted" x-text="'× ' + data.qty + ' item'"></span>
                            <span class="font-semibold" x-text="formatRupiah(data.qty * data.price)"></span>
                        </div>
                    </template>
                    <div x-show="totalItems === 0" class="text-center py-4 text-charcoal-muted text-sm">
                        Belum ada item ditambahkan
                    </div>
                </div>

                <div x-show="totalItems > 0" class="border-t pt-3 mt-3">
                    <div class="flex justify-between font-bold">
                        <span>Total F&B</span>
                        <span class="text-primary" x-text="totalPriceFormatted"></span>
                    </div>
                </div>

                {{-- Kursi info --}}
                <div class="mt-4 p-3 bg-primary/5 rounded-xl border border-primary/10">
                    <p class="text-xs font-semibold text-charcoal mb-1">Kursi Terpilih:</p>
                    @foreach($lockedSeats as $lock)
                        <span class="badge badge-primary text-xs mr-1">{{ $lock->seat->seat_code }}</span>
                    @endforeach
                </div>

                {{-- Form Submit --}}
                <form action="{{ route('booking.checkout') }}" method="GET" id="fnb-form" class="mt-4">
                    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                    <template x-for="(data, id) in cart" :key="id">
                        <input type="hidden" :name="'fnb[' + id + ']'" :value="data.qty">
                    </template>
                    @foreach($lockedSeats as $lock)
                        <input type="hidden" name="seat_ids[]" value="{{ $lock->seat_id }}">
                    @endforeach

                    <button type="submit" class="btn-primary w-full py-3 text-base mt-2">
                        Lanjut ke Checkout
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </form>

                <a href="{{ route('booking.checkout') }}?schedule_id={{ $schedule->id }}&{{ implode('&', array_map(fn($l) => 'seat_ids[]=' . $l->seat_id, $lockedSeats->all())) }}" class="btn-ghost w-full mt-2 text-sm justify-center py-2">
                    Lewati F&B →
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
