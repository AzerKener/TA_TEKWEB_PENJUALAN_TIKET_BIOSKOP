@extends('layouts.app')

@section('title', 'Pilih Kursi — ' . $schedule->movie->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Step Indicator --}}
    <div class="step-indicator max-w-xl mx-auto mb-10">
        @foreach(['Pilih Jadwal', 'Pilih Kursi', 'F&B', 'Checkout'] as $i => $step)
            <div class="step-item {{ $i < 1 ? 'completed' : ($i === 1 ? 'active' : '') }}">
                <div class="step-circle">
                    @if($i < 1)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    @else
                        {{ $i + 1 }}
                    @endif
                </div>
                <span class="text-xs mt-1.5 font-medium {{ $i === 1 ? 'text-primary font-bold' : ($i < 1 ? 'text-green-600' : 'text-charcoal-muted') }}">{{ $step }}</span>
            </div>
        @endforeach
    </div>

    {{-- Movie Info Bar --}}
    <div class="glass rounded-2xl p-4 mb-6 flex flex-wrap items-center gap-4">
        <div class="flex items-center gap-3">
            @if($schedule->movie->poster_image)
                <img src="{{ $schedule->movie->poster_url }}" class="w-12 h-16 rounded-lg object-cover" alt="">
            @endif
            <div>
                <h2 class="font-display font-bold text-charcoal">{{ $schedule->movie->title }}</h2>
                <p class="text-sm text-charcoal-muted">
                    {{ $schedule->studio->name }} · {{ $schedule->show_date->translatedFormat('d F Y') }} · {{ substr($schedule->start_time, 0, 5) }} WIB
                </p>
            </div>
        </div>
        <div class="ml-auto flex flex-wrap gap-2">
            <span class="badge badge-primary">{{ $schedule->studio->type_label }}</span>
            <span class="badge badge-blue">{{ $schedule->language_type_label }}</span>
        </div>
    </div>

    {{-- Alpine.js Seat Map --}}
    <div x-data="seatMap(
        {{ $schedule->id }},
        '{{ route('seats.lock') }}',
        '{{ route('seats.unlock') }}',
        '{{ route('seats.status', $schedule) }}',
        '{{ csrf_token() }}'
    )" x-init="init()">

        <div class="grid lg:grid-cols-3 gap-6">

            {{-- LEFT: Seat Map --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl p-6 shadow-card">

                    {{-- Countdown Timer --}}
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-display font-bold text-charcoal">Peta Kursi</h3>
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-primary/5 rounded-xl border border-primary/20">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="font-mono font-bold text-primary text-sm" x-text="countdownFormatted">10:00</span>
                            <span class="text-xs text-charcoal-muted">tersisa</span>
                        </div>
                    </div>

                    {{-- Loading State --}}
                    <div x-show="loading" class="flex items-center justify-center py-16">
                        <div class="text-center">
                            <svg class="animate-spin w-10 h-10 text-primary mx-auto mb-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <p class="text-charcoal-muted text-sm">Memuat peta kursi...</p>
                        </div>
                    </div>

                    {{-- Screen Indicator --}}
                    <div x-show="!loading" class="mb-6">
                        <div class="screen-indicator h-3 bg-gradient-to-b from-white/60 to-white/10 rounded-t-full mx-auto mb-2 border border-gray-200" style="max-width: 70%"></div>
                        <p class="text-center text-xs text-charcoal-muted font-semibold tracking-widest uppercase">LAYAR</p>
                    </div>

                    {{-- Seat Grid --}}
                    <div x-show="!loading" class="overflow-x-auto">
                        <div class="inline-block min-w-full">
                            <template x-for="(rowSeats, rowLabel) in getSeatRows()" :key="rowLabel">
                                <div class="flex items-center gap-1 mb-1.5">
                                    {{-- Row Label --}}
                                    <span class="w-6 text-center text-xs font-bold text-charcoal-muted flex-shrink-0" x-text="rowLabel"></span>

                                    {{-- Seats --}}
                                    <template x-for="seat in rowSeats" :key="seat.id">
                                        <button
                                            @click="toggleSeat(seat)"
                                            :class="getSeatClass(seat)"
                                            :title="seat.code + ' (' + seat.type + ')'"
                                            :disabled="seat.status === 'booked' || seat.status === 'locked'">
                                            <span x-text="seat.number" class="text-[10px]"></span>
                                        </button>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Legend --}}
                    <div x-show="!loading" class="flex flex-wrap items-center gap-4 mt-6 pt-4 border-t border-gray-100 text-xs text-charcoal-muted">
                        @foreach([
                            ['class' => 'w-5 h-4 rounded-t-md bg-green-100 border border-green-300', 'label' => 'Tersedia'],
                            ['class' => 'w-5 h-4 rounded-t-md bg-primary', 'label' => 'Dipilih'],
                            ['class' => 'w-5 h-4 rounded-t-md bg-gray-300', 'label' => 'Terisi'],
                            ['class' => 'w-5 h-4 rounded-t-md bg-yellow-200', 'label' => 'Dikunci'],
                            ['class' => 'w-5 h-4 rounded-t-md bg-yellow-100 border border-yellow-300', 'label' => 'VIP'],
                            ['class' => 'w-5 h-4 rounded-t-md bg-pink-100 border border-pink-300', 'label' => 'Couple'],
                        ] as $legend)
                            <div class="flex items-center gap-1.5">
                                <div class="{{ $legend['class'] }}"></div>
                                <span>{{ $legend['label'] }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Harga info --}}
                    <div x-show="!loading" class="mt-4 grid grid-cols-3 gap-2 text-xs">
                        <div class="bg-cream rounded-lg p-2 text-center">
                            <p class="font-semibold text-charcoal">Regular</p>
                            <p class="text-primary font-bold">Rp {{ number_format($schedule->price_regular, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-2 text-center border border-yellow-200">
                            <p class="font-semibold text-charcoal">VIP</p>
                            <p class="text-yellow-700 font-bold">Rp {{ number_format($schedule->price_vip, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-pink-50 rounded-lg p-2 text-center border border-pink-200">
                            <p class="font-semibold text-charcoal">Couple</p>
                            <p class="text-pink-700 font-bold">Rp {{ number_format($schedule->price_couple, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Summary Panel --}}
            <div>
                <div class="bg-white rounded-2xl p-5 shadow-card sticky top-20">
                    <h3 class="font-display font-bold text-charcoal mb-4">Kursi Dipilih</h3>

                    {{-- Selected Seats List --}}
                    <div class="space-y-2 min-h-[80px]">
                        <template x-for="seat in seats.filter(s => selectedSeats.includes(s.id))" :key="seat.id">
                            <div class="flex items-center justify-between py-2 px-3 bg-primary/5 rounded-xl border border-primary/10">
                                <div class="flex items-center gap-2">
                                    <span class="w-7 h-7 bg-primary text-white rounded-lg flex items-center justify-center text-xs font-bold" x-text="seat.code"></span>
                                    <div>
                                        <p class="text-sm font-semibold text-charcoal capitalize" x-text="seat.type"></p>
                                        <p class="text-xs text-charcoal-muted">Baris <span x-text="seat.row"></span></p>
                                    </div>
                                </div>
                                <p class="text-sm font-bold text-primary"
                                   x-text="formatRupiah(seat.type === 'vip' ? schedule.price_vip : seat.type === 'couple' ? schedule.price_couple : schedule.price_regular)">
                                </p>
                            </div>
                        </template>

                        <div x-show="selectedSeats.length === 0" class="text-center py-6 text-charcoal-muted">
                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                            <p class="text-sm">Belum ada kursi dipilih</p>
                        </div>
                    </div>

                    {{-- Total --}}
                    <div x-show="selectedSeats.length > 0" class="border-t border-gray-100 pt-4 mt-4">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm text-charcoal-muted">Subtotal</span>
                            <span class="font-bold text-charcoal" x-text="totalPriceFormatted"></span>
                        </div>
                        <p class="text-xs text-charcoal-muted mb-4">Belum termasuk pajak dan F&B</p>

                        {{-- Next Step Form --}}
                        <form action="{{ route('booking.fnb', $schedule) }}" method="GET" id="seat-form">
                            <template x-for="seatId in selectedSeats" :key="seatId">
                                <input type="hidden" name="seat_ids[]" :value="seatId">
                            </template>
                        </form>

                        <button @click="selectedSeats.length > 0 && document.getElementById('seat-form').submit()"
                                class="btn-primary w-full py-3 text-base"
                                :disabled="selectedSeats.length === 0"
                                :class="selectedSeats.length === 0 && 'opacity-50 cursor-not-allowed'">
                            Lanjut ke F&B
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    {{-- Back --}}
                    <a href="{{ route('booking.schedule', $schedule->movie) }}" class="btn-ghost w-full mt-2 py-2 text-sm justify-center">
                        ← Ganti Jadwal
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.formatRupiah = (amount) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
    };
</script>
@endpush
