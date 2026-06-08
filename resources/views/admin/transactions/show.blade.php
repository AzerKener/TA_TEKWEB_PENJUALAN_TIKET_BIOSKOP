@extends('layouts.admin')
@section('title', 'Detail Transaksi — ' . $transaction->transaction_code)

@section('content')
<div class="max-w-3xl">
    <div class="flex items-center gap-3 mb-5">
        <a href="{{ route('admin.transactions.index') }}" class="btn-secondary btn-sm">← Kembali</a>
    </div>

    <div class="grid md:grid-cols-3 gap-5">
        {{-- Main card --}}
        <div class="md:col-span-2 space-y-5">
            <div class="bg-white rounded-2xl shadow-card overflow-hidden">
                <div class="bg-espresso px-5 py-4 text-white flex items-center justify-between">
                    <div>
                        <p class="text-xs text-white/60">Kode Transaksi</p>
                        <p class="font-mono font-bold text-lg">{{ $transaction->transaction_code }}</p>
                    </div>
                    <span class="badge badge-{{ $transaction->status_color }} text-sm">{{ $transaction->status_label }}</span>
                </div>

                <div class="p-5 border-b border-dashed border-gray-200">
                    <h2 class="font-display font-bold text-xl text-charcoal">{{ $transaction->schedule->movie->title }}</h2>
                    <div class="grid grid-cols-2 gap-2 mt-3 text-sm">
                        @foreach([
                            ['📅 Tanggal', $transaction->schedule->show_date->translatedFormat('d F Y')],
                            ['🕐 Jam', substr($transaction->schedule->start_time, 0, 5) . ' — ' . substr($transaction->schedule->end_time, 0, 5)],
                            ['🏛️ Studio', $transaction->schedule->studio->name],
                            ['🎭 Tipe', $transaction->schedule->language_type_label],
                        ] as [$label, $val])
                            <div>
                                <p class="text-xs text-charcoal-muted">{{ $label }}</p>
                                <p class="font-semibold text-charcoal">{{ $val }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Seats --}}
                <div class="p-5 border-b border-dashed border-gray-200">
                    <p class="text-xs font-semibold text-charcoal-muted uppercase tracking-wider mb-3">Kursi Dipesan</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($transaction->tickets as $ticket)
                            <div class="bg-primary/5 rounded-xl px-3 py-2 border border-primary/10 text-center">
                                <span class="font-display font-black text-lg text-primary">{{ $ticket->seat->seat_code }}</span>
                                <span class="block text-xs text-charcoal-muted capitalize">{{ $ticket->seat_type }}</span>
                                <span class="block text-xs font-bold">Rp {{ number_format($ticket->price, 0, ',', '.') }}</span>
                                <span class="block font-mono text-xs text-charcoal-muted mt-0.5">{{ $ticket->ticket_code }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- F&B --}}
                @if($transaction->fnbItems->count() > 0)
                    <div class="p-5 border-b border-dashed border-gray-200">
                        <p class="text-xs font-semibold text-charcoal-muted uppercase tracking-wider mb-3">Makanan & Minuman</p>
                        <div class="space-y-2">
                            @foreach($transaction->fnbItems as $item)
                                <div class="flex justify-between text-sm">
                                    <span>{{ $item->fnbItem->name }} × {{ $item->quantity }}</span>
                                    <span class="font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Totals --}}
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
                            <span>TOTAL</span>
                            <span class="text-primary">{{ $transaction->total_formatted }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Side Info --}}
        <div class="space-y-4">
            {{-- Customer --}}
            <div class="bg-white rounded-2xl p-5 shadow-card">
                <p class="text-xs font-semibold text-charcoal-muted uppercase tracking-wider mb-3">Customer</p>
                <div class="flex items-center gap-3">
                    <img src="{{ $transaction->user->avatar_url }}" class="w-10 h-10 rounded-xl" alt="">
                    <div>
                        <p class="font-semibold text-charcoal">{{ $transaction->user->name }}</p>
                        <p class="text-xs text-charcoal-muted">{{ $transaction->user->email }}</p>
                    </div>
                </div>
            </div>

            {{-- Payment --}}
            <div class="bg-white rounded-2xl p-5 shadow-card">
                <p class="text-xs font-semibold text-charcoal-muted uppercase tracking-wider mb-3">Pembayaran</p>
                <div class="space-y-2 text-sm">
                    <div><p class="text-xs text-charcoal-muted">Metode</p><p class="font-semibold">{{ $transaction->payment_method_label }}</p></div>
                    @if($transaction->paid_at)
                        <div><p class="text-xs text-charcoal-muted">Dibayar Pada</p><p class="font-semibold">{{ $transaction->paid_at->translatedFormat('d F Y H:i') }}</p></div>
                    @endif
                    <div><p class="text-xs text-charcoal-muted">Dibuat</p><p class="font-semibold">{{ $transaction->created_at->translatedFormat('d F Y H:i') }}</p></div>
                </div>
            </div>

            {{-- Update Status --}}
            <div class="bg-white rounded-2xl p-5 shadow-card">
                <p class="text-xs font-semibold text-charcoal-muted uppercase tracking-wider mb-3">Update Status</p>
                <form action="{{ route('admin.transactions.status', $transaction) }}" method="POST" class="space-y-2">
                    @csrf @method('PATCH')
                    <select name="status" class="form-input w-full text-sm">
                        @foreach(['pending' => 'Pending', 'paid' => 'Lunas', 'failed' => 'Gagal', 'expired' => 'Kadaluarsa', 'refunded' => 'Dikembalikan'] as $v => $l)
                            <option value="{{ $v }}" {{ $transaction->payment_status === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary w-full text-sm justify-center">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
