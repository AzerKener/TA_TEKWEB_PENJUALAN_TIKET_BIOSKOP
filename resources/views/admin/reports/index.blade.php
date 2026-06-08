@extends('layouts.admin')
@section('title', 'Laporan & Analitik')

@section('content')

{{-- Date Filter --}}
<form method="GET" class="bg-white rounded-2xl p-4 shadow-card mb-6">
    <div class="flex flex-wrap items-end gap-3">
        <div>
            <label class="form-label text-xs">Dari Tanggal</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="form-input">
        </div>
        <div>
            <label class="form-label text-xs">Sampai Tanggal</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="form-input">
        </div>
        <button type="submit" class="btn-primary">📊 Tampilkan</button>
        <a href="{{ route('admin.reports.export', array_merge(request()->query())) }}"
           class="btn-secondary">
            📥 Export CSV
        </a>
    </div>
    <p class="text-xs text-charcoal-muted mt-2">
        Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} — {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
    </p>
</form>

{{-- Period KPIs --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['label' => 'Total Pendapatan', 'value' => 'Rp ' . number_format($periodRevenue, 0, ',', '.'), 'icon' => '💰', 'color' => 'text-green-700', 'border' => 'border-green-200'],
        ['label' => 'Transaksi Lunas', 'value' => number_format($periodCount), 'icon' => '✅', 'color' => 'text-primary', 'border' => 'border-primary/20'],
        ['label' => 'Rata-rata per Transaksi', 'value' => 'Rp ' . ($periodCount > 0 ? number_format($periodRevenue / $periodCount, 0, ',', '.') : 0), 'icon' => '📊', 'color' => 'text-blue-700', 'border' => 'border-blue-200'],
        ['label' => 'Tiket Terjual', 'value' => number_format($totalTicketsSold), 'icon' => '🎟️', 'color' => 'text-purple-700', 'border' => 'border-purple-200'],
    ] as $kpi)
        <div class="bg-white rounded-2xl p-5 shadow-card border {{ $kpi['border'] }}">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $kpi['label'] }}</p>
                <span class="text-2xl">{{ $kpi['icon'] }}</span>
            </div>
            <p class="font-display font-bold text-2xl {{ $kpi['color'] }}">{{ $kpi['value'] }}</p>
        </div>
    @endforeach
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-6">
    {{-- Daily Revenue Chart --}}
    <div class="bg-white rounded-2xl p-5 shadow-card xl:col-span-2">
        <h3 class="font-display font-bold text-charcoal mb-4">Pendapatan per Hari (Periode Terpilih)</h3>
        <canvas id="reportChart" height="70"></canvas>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
    {{-- Payment Methods --}}
    <div class="bg-white rounded-2xl p-5 shadow-card">
        <h3 class="font-display font-bold text-charcoal mb-4">Metode Pembayaran</h3>
        @if($paymentMethodStats->count() > 0)
            <div class="space-y-3">
                @php $maxRev = $paymentMethodStats->max('total'); @endphp
                @foreach($paymentMethodStats as $stat)
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium capitalize text-charcoal">
                                {{ str_replace('_', ' ', ucfirst($stat->payment_method)) }}
                            </span>
                            <div class="text-right">
                                <span class="text-sm font-bold">Rp {{ number_format($stat->total, 0, ',', '.') }}</span>
                                <span class="text-xs text-charcoal-muted ml-2">({{ $stat->count }}x)</span>
                            </div>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full">
                            <div class="h-2 bg-primary rounded-full transition-all"
                                 style="width: {{ $maxRev > 0 ? ($stat->total / $maxRev * 100) : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-charcoal-muted text-sm text-center py-4">Tidak ada data untuk periode ini.</p>
        @endif
    </div>

    {{-- Top Movies by Revenue --}}
    <div class="bg-white rounded-2xl p-5 shadow-card">
        <h3 class="font-display font-bold text-charcoal mb-4">Top Film by Pendapatan 🏆</h3>
        @if($topMoviesByRevenue->count() > 0)
            <div class="space-y-3">
                @foreach($topMoviesByRevenue as $i => $movie)
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0
                            {{ $i === 0 ? 'bg-yellow-400 text-charcoal' : ($i === 1 ? 'bg-gray-300 text-gray-700' : ($i === 2 ? 'bg-orange-300 text-orange-800' : 'bg-gray-100 text-gray-500')) }}">
                            {{ $i + 1 }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-charcoal truncate">{{ $movie->title }}</p>
                            <p class="text-xs text-charcoal-muted">{{ $movie->trx_count }} transaksi</p>
                        </div>
                        <span class="text-sm font-bold text-primary flex-shrink-0">
                            Rp {{ number_format($movie->revenue, 0, ',', '.') }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-charcoal-muted text-sm text-center py-4">Tidak ada data.</p>
        @endif
    </div>
</div>

{{-- Studio Occupancy --}}
@if(isset($studioOccupancy) && $studioOccupancy->count() > 0)
<div class="bg-white rounded-2xl p-5 shadow-card">
    <h3 class="font-display font-bold text-charcoal mb-4">Tingkat Okupansi Studio</h3>
    <div class="space-y-3">
        @foreach($studioOccupancy as $studio)
            <div class="flex items-center gap-3">
                <span class="w-32 text-sm font-medium text-charcoal flex-shrink-0">{{ $studio->name }}</span>
                <div class="flex-1 h-4 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-4 rounded-full transition-all {{ $studio->occupancy > 75 ? 'bg-green-500' : ($studio->occupancy > 40 ? 'bg-yellow-400' : 'bg-red-400') }}"
                         style="width: {{ min(100, $studio->occupancy ?? 0) }}%"></div>
                </div>
                <span class="text-sm font-bold text-charcoal w-12 text-right flex-shrink-0">
                    {{ number_format($studio->occupancy ?? 0, 1) }}%
                </span>
            </div>
        @endforeach
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
const chartData = @json($dailyRevenue);
new Chart(document.getElementById('reportChart'), {
    type: 'bar',
    data: {
        labels: chartData.map(d => {
            const date = new Date(d.date);
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
        }),
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: chartData.map(d => d.revenue),
            backgroundColor: 'rgba(128, 0, 0, 0.12)',
            borderColor: '#800000',
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
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
                    callback: (v) => 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(v)
                }
            },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endpush
