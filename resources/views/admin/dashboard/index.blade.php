@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

{{-- KPI Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
    @php
        $kpis = [
            ['label' => 'Total Pendapatan', 'value' => 'Rp ' . number_format($totalRevenue, 0, ',', '.'), 'icon' => '💰', 'color' => 'text-green-600', 'bg' => 'bg-green-50', 'border' => 'border-green-200', 'sub' => 'Dari transaksi lunas'],
            ['label' => 'Tiket Terjual', 'value' => number_format($totalTickets), 'icon' => '🎟️', 'color' => 'text-blue-600', 'bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'sub' => 'Tiket aktif'],
            ['label' => 'Total Customer', 'value' => number_format($totalCustomers), 'icon' => '👥', 'color' => 'text-purple-600', 'bg' => 'bg-purple-50', 'border' => 'border-purple-200', 'sub' => 'Pengguna terdaftar'],
            ['label' => 'Film Tayang', 'value' => $totalMovies, 'icon' => '🎬', 'color' => 'text-primary', 'bg' => 'bg-primary/5', 'border' => 'border-primary/20', 'sub' => 'Sedang tayang'],
        ];
    @endphp

    @foreach($kpis as $kpi)
        <div class="bg-white rounded-2xl p-5 border {{ $kpi['border'] }} shadow-card hover:shadow-card-hover transition-all">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $kpi['label'] }}</p>
                    <p class="font-display font-bold text-2xl mt-1 {{ $kpi['color'] }}">{{ $kpi['value'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $kpi['sub'] }}</p>
                </div>
                <div class="w-12 h-12 {{ $kpi['bg'] }} rounded-xl flex items-center justify-center text-2xl flex-shrink-0">
                    {{ $kpi['icon'] }}
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

    {{-- Revenue Chart --}}
    <div class="xl:col-span-2 bg-white rounded-2xl p-5 border border-gray-100 shadow-card">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-display font-bold text-charcoal">Pendapatan 7 Hari Terakhir</h3>
                <p class="text-xs text-gray-400">Riwayat transaksi yang lunas</p>
            </div>
            <span class="badge badge-green">Live</span>
        </div>
        <canvas id="revenueChart" height="100"></canvas>
    </div>

    {{-- Top Movies --}}
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-card">
        <h3 class="font-display font-bold text-charcoal mb-4">Top Film 🏆</h3>
        <div class="space-y-3">
            @foreach($topMovies->take(5) as $i => $movie)
                <div class="flex items-center gap-3 group">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0
                        {{ $i === 0 ? 'bg-gold text-charcoal' : ($i === 1 ? 'bg-gray-200 text-gray-600' : ($i === 2 ? 'bg-orange-200 text-orange-700' : 'bg-gray-100 text-gray-500')) }}">
                        {{ $i + 1 }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-charcoal truncate">{{ $movie->title }}</p>
                        <p class="text-xs text-gray-400">{{ number_format($movie->ticket_count) }} tiket</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Today's Schedules --}}
<div class="mt-5 bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-display font-bold text-charcoal">Jadwal Hari Ini ({{ today()->translatedFormat('d F Y') }})</h3>
        <a href="{{ route('admin.schedules.index', ['date' => today()->format('Y-m-d')]) }}" class="text-xs text-primary font-medium hover:underline">Lihat semua</a>
    </div>
    <div class="overflow-x-auto">
        <table class="table-admin">
            <thead>
                <tr>
                    <th>Film</th>
                    <th>Studio</th>
                    <th>Jam Tayang</th>
                    <th>Harga</th>
                    <th>Kursi Tersisa</th>
                    <th>Okupansi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($todaySchedules as $schedule)
                    <tr>
                        <td class="font-medium">{{ $schedule->movie->title ?? '-' }}</td>
                        <td><span class="badge badge-primary">{{ $schedule->studio->name ?? '-' }}</span></td>
                        <td>
                            <span class="font-mono text-sm">{{ substr($schedule->start_time, 0, 5) }} — {{ substr($schedule->end_time, 0, 5) }}</span>
                        </td>
                        <td>Rp {{ number_format($schedule->price_regular, 0, ',', '.') }}</td>
                        <td>
                            <span class="{{ $schedule->available_seats_count < 10 ? 'text-red-600 font-bold' : 'text-green-600 font-semibold' }}">
                                {{ $schedule->available_seats_count }}
                            </span>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-100 rounded-full h-1.5 w-20">
                                    <div class="h-1.5 rounded-full {{ $schedule->occupancy_rate > 80 ? 'bg-red-500' : ($schedule->occupancy_rate > 50 ? 'bg-yellow-500' : 'bg-green-500') }}"
                                         style="width: {{ min(100, $schedule->occupancy_rate) }}%"></div>
                                </div>
                                <span class="text-xs font-semibold">{{ $schedule->occupancy_rate }}%</span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-400 py-8">Tidak ada jadwal hari ini</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Recent Transactions --}}
<div class="mt-5 bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-display font-bold text-charcoal">Transaksi Terbaru</h3>
        <a href="{{ route('admin.transactions.index') }}" class="text-xs text-primary font-medium hover:underline">Lihat semua</a>
    </div>
    <div class="overflow-x-auto">
        <table class="table-admin">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Customer</th>
                    <th>Film</th>
                    <th>Total</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentTransactions as $trx)
                    <tr>
                        <td><span class="font-mono text-xs">{{ $trx->transaction_code }}</span></td>
                        <td class="font-medium">{{ $trx->user->name ?? '-' }}</td>
                        <td class="text-sm">{{ Str::limit($trx->schedule->movie->title ?? '-', 25) }}</td>
                        <td class="font-semibold">{{ $trx->total_formatted }}</td>
                        <td class="text-sm capitalize">{{ $trx->payment_method_label }}</td>
                        <td>
                            <span class="badge badge-{{ $trx->status_color }}">{{ $trx->status_label }}</span>
                        </td>
                        <td class="text-xs text-gray-400">{{ $trx->created_at->diffForHumans() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Revenue Chart
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const chartData = @json($revenueChart);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.map(d => {
                const date = new Date(d.date);
                return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
            }),
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: chartData.map(d => d.total),
                fill: true,
                backgroundColor: 'rgba(128, 0, 0, 0.08)',
                borderColor: '#800000',
                borderWidth: 2.5,
                pointBackgroundColor: '#800000',
                pointRadius: 4,
                pointHoverRadius: 7,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y)
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: {
                        callback: (val) => 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(val)
                    }
                },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endpush
