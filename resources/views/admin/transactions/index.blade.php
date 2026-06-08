@extends('layouts.admin')
@section('title', 'Manajemen Transaksi')

@section('content')
{{-- Stats Row --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['label' => 'Total Transaksi', 'value' => $stats['total'], 'color' => 'text-charcoal', 'bg' => 'bg-gray-50', 'border' => 'border-gray-200'],
        ['label' => 'Lunas', 'value' => $stats['paid'], 'color' => 'text-green-700', 'bg' => 'bg-green-50', 'border' => 'border-green-200'],
        ['label' => 'Pending', 'value' => $stats['pending'], 'color' => 'text-yellow-700', 'bg' => 'bg-yellow-50', 'border' => 'border-yellow-200'],
        ['label' => 'Total Pendapatan', 'value' => 'Rp ' . number_format($stats['revenue'], 0, ',', '.'), 'color' => 'text-primary', 'bg' => 'bg-primary/5', 'border' => 'border-primary/20'],
    ] as $stat)
        <div class="bg-white rounded-2xl p-4 shadow-card border {{ $stat['border'] }}">
            <p class="text-xs text-gray-500 uppercase tracking-wider">{{ $stat['label'] }}</p>
            <p class="font-display font-bold text-xl {{ $stat['color'] }} mt-0.5">{{ $stat['value'] }}</p>
        </div>
    @endforeach
</div>

{{-- Filters --}}
<form method="GET" class="bg-white rounded-2xl p-4 shadow-card mb-5">
    <div class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="form-label text-xs">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Kode / nama customer..." class="form-input max-w-xs">
        </div>
        <div>
            <label class="form-label text-xs">Status</label>
            <select name="status" class="form-input max-w-xs">
                <option value="">Semua Status</option>
                @foreach(['pending' => 'Pending', 'paid' => 'Lunas', 'failed' => 'Gagal', 'expired' => 'Kadaluarsa', 'refunded' => 'Dikembalikan'] as $v => $l)
                    <option value="{{ $v }}" {{ request('status') === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label text-xs">Dari</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input">
        </div>
        <div>
            <label class="form-label text-xs">Sampai</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input">
        </div>
        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
            <a href="{{ route('admin.transactions.index') }}" class="btn-secondary">Reset</a>
        @endif
    </div>
</form>

<div class="bg-white rounded-2xl shadow-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="table-admin">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Customer</th>
                    <th>Film</th>
                    <th>Kursi</th>
                    <th>Total</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th>Waktu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                    <tr>
                        <td class="font-mono text-xs font-bold text-charcoal">{{ $trx->transaction_code }}</td>
                        <td>
                            <p class="font-medium text-charcoal">{{ $trx->user->name ?? '-' }}</p>
                            <p class="text-xs text-charcoal-muted">{{ $trx->user->email ?? '' }}</p>
                        </td>
                        <td class="text-sm max-w-[180px]">
                            <span class="line-clamp-1">{{ $trx->schedule->movie->title ?? '-' }}</span>
                            <p class="text-xs text-charcoal-muted">{{ $trx->schedule->show_date->format('d/m/Y') ?? '' }}</p>
                        </td>
                        <td class="text-sm text-center">{{ $trx->tickets->count() }}</td>
                        <td class="font-bold text-charcoal">{{ $trx->total_formatted }}</td>
                        <td class="text-sm text-charcoal-muted">{{ $trx->payment_method_label }}</td>
                        <td>
                            <span class="badge badge-{{ $trx->status_color }}">{{ $trx->status_label }}</span>
                        </td>
                        <td class="text-xs text-charcoal-muted">{{ $trx->created_at->format('d/m H:i') }}</td>
                        <td>
                            <div class="flex gap-1.5">
                                <a href="{{ route('admin.transactions.show', $trx) }}"
                                   class="btn btn-sm bg-blue-50 text-blue-600 hover:bg-blue-100">Detail</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-10 text-gray-400">Tidak ada transaksi</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $transactions->links() }}</div>
</div>
@endsection
