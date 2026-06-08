<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticket {{ $transaction->transaction_code }} — CineXpress</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@700;900&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body { background: #f5f5f5; }
        .ticket-perforated {
            position: relative;
        }
        .ticket-perforated::before,
        .ticket-perforated::after {
            content: '';
            position: absolute;
            left: -16px;
            right: -16px;
            height: 2px;
            background-image: repeating-linear-gradient(90deg, #e5e7eb 0, #e5e7eb 8px, transparent 8px, transparent 16px);
        }
        .ticket-perforated::before { top: 0; }
        .ticket-perforated::after { bottom: 0; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 font-sans">

    {{-- Print button --}}
    <div class="fixed top-4 right-4 z-10 no-print flex gap-2">
        <button onclick="window.print()" class="btn-primary">
            🖨️ Cetak E-Ticket
        </button>
        <a href="{{ route('my.transactions') }}" class="btn-secondary">
            ← Kembali
        </a>
    </div>

    {{-- E-Ticket Card --}}
    <div class="eticket-card w-full max-w-md bg-white rounded-3xl overflow-hidden shadow-2xl" style="box-shadow: 0 20px 60px rgba(0,0,0,0.15);">

        {{-- Top: Movie Section --}}
        <div class="bg-espresso text-white p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 bg-white/20 rounded-lg flex items-center justify-center">
                        🎬
                    </div>
                    <span class="font-display font-bold">CineXpress</span>
                </div>
                <span class="badge bg-green-400/20 text-green-300 border border-green-400/30 text-xs">
                    ✓ VALID
                </span>
            </div>

            <h1 class="font-display text-2xl font-black leading-tight mb-1">
                {{ $transaction->schedule->movie->title }}
            </h1>
            <div class="flex flex-wrap gap-2 text-sm text-white/70">
                <span>{{ $transaction->schedule->movie->rating }}</span>
                <span>·</span>
                <span>{{ $transaction->schedule->movie->duration_formatted }}</span>
                <span>·</span>
                <span>{{ $transaction->schedule->movie->language }}</span>
            </div>
        </div>

        {{-- Show Info --}}
        <div class="p-5 border-b border-dashed border-gray-200">
            <div class="grid grid-cols-2 gap-4">
                @foreach([
                    ['📅 Tanggal', $transaction->schedule->show_date->translatedFormat('d F Y')],
                    ['🕐 Jam', substr($transaction->schedule->start_time, 0, 5) . ' WIB'],
                    ['🏛️ Studio', $transaction->schedule->studio->name],
                    ['🎭 Tipe', $transaction->schedule->studio->type_label],
                ] as [$label, $value])
                    <div>
                        <p class="text-xs text-charcoal-muted">{{ $label }}</p>
                        <p class="font-bold text-charcoal">{{ $value }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Seats --}}
        <div class="p-5 border-b border-dashed border-gray-200">
            <p class="text-xs font-semibold text-charcoal-muted uppercase tracking-wider mb-3">Kursi Anda</p>
            <div class="flex flex-wrap gap-2">
                @foreach($transaction->tickets as $ticket)
                    <div class="flex flex-col items-center px-4 py-2 bg-primary/5 rounded-xl border-2 border-primary/20">
                        <span class="font-display font-black text-2xl text-primary">{{ $ticket->seat->seat_code }}</span>
                        <span class="text-xs text-charcoal-muted capitalize">{{ $ticket->seat_type }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- QR Code + Booking Code --}}
        <div class="p-5 text-center bg-cream/50">
            {{-- QR Code placeholder --}}
            <div class="w-32 h-32 mx-auto mb-3 bg-white rounded-xl border-2 border-gray-200 flex items-center justify-center">
                <div class="text-center">
                    <div class="grid grid-cols-5 gap-0.5 w-24">
                        @for($row = 0; $row < 5; $row++)
                            @for($col = 0; $col < 5; $col++)
                                <div class="w-4 h-4 {{ (($row + $col) % 2 === 0 || $row === 0 || $row === 4 || $col === 0 || $col === 4) ? 'bg-charcoal' : 'bg-white' }} rounded-sm"></div>
                            @endfor
                        @endfor
                    </div>
                </div>
            </div>
            <p class="font-mono font-bold text-lg text-charcoal tracking-widest">
                {{ $transaction->transaction_code }}
            </p>
            <p class="text-xs text-charcoal-muted mt-1">Tunjukkan barcode ini kepada petugas</p>
        </div>

        {{-- Footer --}}
        <div class="px-5 py-4 bg-charcoal text-white text-center">
            <p class="text-xs text-white/60">Nama Pemegang</p>
            <p class="font-bold">{{ $transaction->user->name }}</p>
            <p class="text-xs text-white/40 mt-2">
                Diterbitkan: {{ $transaction->paid_at?->translatedFormat('d F Y H:i') ?? $transaction->created_at->translatedFormat('d F Y H:i') }} WIB
            </p>
        </div>
    </div>

    {{-- Terms --}}
    <div class="w-full max-w-md mt-4 text-center no-print">
        <p class="text-xs text-gray-400">Tiket tidak dapat dipindahtangankan. Harap tiba 30 menit sebelum pemutaran.</p>
    </div>

</body>
</html>
