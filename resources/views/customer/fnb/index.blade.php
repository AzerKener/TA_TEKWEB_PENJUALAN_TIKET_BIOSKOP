@extends('layouts.app')

@section('title', 'Makanan & Minuman')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="text-center mb-10">
        <h1 class="section-title mb-2">Makanan & Minuman</h1>
        <p class="section-subtitle">Lengkapi pengalaman menonton dengan pilihan F&B premium kami</p>
    </div>

    {{-- Category Navigation --}}
    <div class="flex gap-2 justify-center mb-10 flex-wrap">
        @foreach(['food' => '🍔 Makanan', 'drink' => '🥤 Minuman', 'snack' => '🍟 Snack', 'combo' => '🎁 Paket Combo'] as $cat => $label)
            <a href="#{{ $cat }}"
               class="px-4 py-2 bg-white rounded-xl shadow-card border border-transparent hover:border-primary/30 hover:bg-primary/5 transition-all text-sm font-semibold text-charcoal">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- F&B Categories --}}
    @foreach($fnbItems as $category => $items)
        <section id="{{ $category }}" class="mb-12 scroll-mt-24">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-xl">
                    {{ $category === 'food' ? '🍔' : ($category === 'drink' ? '🥤' : ($category === 'combo' ? '🎁' : '🍟')) }}
                </div>
                <div>
                    <h2 class="font-display font-bold text-xl text-charcoal">{{ $items->first()->category_label }}</h2>
                    <p class="text-xs text-charcoal-muted">{{ $items->count() }} pilihan tersedia</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($items as $item)
                    <div class="card group overflow-hidden">
                        {{-- Image --}}
                        <div class="aspect-video bg-cream-dark overflow-hidden">
                            @if($item->image)
                                <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-4xl bg-gradient-to-br from-cream to-cream-dark">
                                    {{ $category === 'drink' ? '🥤' : '🍿' }}
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="p-4">
                            <h3 class="font-semibold text-charcoal mb-1 line-clamp-2">{{ $item->name }}</h3>
                            @if($item->description)
                                <p class="text-xs text-charcoal-muted mb-2 line-clamp-2">{{ $item->description }}</p>
                            @endif
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-primary">{{ $item->price_formatted }}</span>
                                @if($item->stock < 20)
                                    <span class="badge badge-yellow text-xs">Terbatas</span>
                                @else
                                    <span class="badge badge-green text-xs">Tersedia</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endforeach

    {{-- CTA --}}
    <div class="text-center py-10 bg-espresso rounded-3xl text-white">
        <div class="text-5xl mb-4">🎬</div>
        <h3 class="font-display text-2xl font-bold mb-2">Siap Nonton?</h3>
        <p class="text-white/70 mb-6">Tambahkan F&B favorit saat memesan tiket untuk pengalaman menonton yang sempurna.</p>
        <a href="{{ route('movies.index', ['status' => 'now_playing']) }}" class="btn-gold btn-lg">
            Pesan Tiket + F&B Sekarang
        </a>
    </div>
</div>
@endsection
