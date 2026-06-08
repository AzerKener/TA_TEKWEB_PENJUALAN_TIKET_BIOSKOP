@extends('layouts.admin')
@section('title', 'Kursi — ' . $studio->name)

@section('content')
<div class="flex items-center gap-3 mb-5">
    <a href="{{ route('admin.studios.index') }}" class="btn-secondary btn-sm">← Kembali</a>
    <h2 class="font-display font-bold text-xl text-charcoal">{{ $studio->name }} — Denah Kursi</h2>
</div>

{{-- Seat Map --}}
<div class="bg-white rounded-2xl p-6 shadow-card">
    {{-- Screen --}}
    <div class="h-3 bg-gradient-to-b from-primary/30 to-transparent rounded-t-full mx-auto mb-2 border border-primary/20" style="max-width: 60%"></div>
    <p class="text-center text-xs text-charcoal-muted font-semibold tracking-widest uppercase mb-8">LAYAR</p>

    {{-- Legend --}}
    <div class="flex flex-wrap gap-4 justify-center mb-6 text-xs text-charcoal-muted">
        @foreach([['bg-green-100 border border-green-300', 'Regular'], ['bg-yellow-100 border border-yellow-300', 'VIP'], ['bg-pink-100 border border-pink-300', 'Couple'], ['bg-gray-200', 'Nonaktif']] as [$cls, $lbl])
            <div class="flex items-center gap-1.5">
                <div class="w-5 h-4 rounded-t-md {{ $cls }}"></div>
                <span>{{ $lbl }}</span>
            </div>
        @endforeach
    </div>

    {{-- Grid --}}
    @php
        $seatsByRow = $studio->seats->groupBy('row_label');
    @endphp

    <div class="overflow-x-auto">
        <div class="inline-block min-w-full">
            @foreach($seatsByRow as $row => $seats)
                <div class="flex items-center gap-1 mb-1.5 justify-center">
                    <span class="w-6 text-xs font-bold text-charcoal-muted text-center flex-shrink-0">{{ $row }}</span>
                    @foreach($seats->sortBy('seat_number') as $seat)
                        <div class="w-8 h-7 rounded-t-md flex items-center justify-center text-xs font-bold cursor-default
                            {{ !$seat->is_active ? 'bg-gray-200 text-gray-400' :
                               ($seat->type === 'vip' ? 'bg-yellow-100 border border-yellow-300 text-yellow-800' :
                               ($seat->type === 'couple' ? 'bg-pink-100 border border-pink-300 text-pink-800' :
                               'bg-green-100 border border-green-300 text-green-800')) }}"
                             title="{{ $seat->seat_code }} ({{ ucfirst($seat->type) }})">
                            {{ $seat->seat_number }}
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    {{-- Stats --}}
    <div class="flex flex-wrap justify-center gap-6 mt-8 text-sm border-t pt-5">
        @foreach([
            ['Regular', $studio->seats->where('type', 'regular')->where('is_active', true)->count(), 'text-green-700'],
            ['VIP', $studio->seats->where('type', 'vip')->where('is_active', true)->count(), 'text-yellow-700'],
            ['Couple', $studio->seats->where('type', 'couple')->where('is_active', true)->count(), 'text-pink-700'],
            ['Total Aktif', $studio->capacity, 'text-primary font-bold'],
        ] as [$label, $count, $color])
            <div class="text-center">
                <p class="text-2xl font-display font-black {{ $color }}">{{ $count }}</p>
                <p class="text-xs text-charcoal-muted">{{ $label }}</p>
            </div>
        @endforeach
    </div>
</div>
@endsection
