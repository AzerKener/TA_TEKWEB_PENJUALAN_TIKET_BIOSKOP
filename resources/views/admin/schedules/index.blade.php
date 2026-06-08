@extends('layouts.admin')
@section('title', 'Jadwal Tayang')

@section('content')
<div class="flex justify-between items-center mb-5">
    <p class="text-sm text-gray-500">{{ $schedules->total() }} jadwal ditemukan</p>
    <a href="{{ route('admin.schedules.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Jadwal
    </a>
</div>

<form method="GET" class="bg-white rounded-2xl p-4 shadow-card mb-5">
    <div class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="form-label text-xs">Film</label>
            <select name="movie_id" class="form-input max-w-xs">
                <option value="">Semua Film</option>
                @foreach($movies as $m)
                    <option value="{{ $m->id }}" {{ request('movie_id') == $m->id ? 'selected' : '' }}>
                        {{ $m->title }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label text-xs">Studio</label>
            <select name="studio_id" class="form-input max-w-xs">
                <option value="">Semua Studio</option>
                @foreach($studios as $s)
                    <option value="{{ $s->id }}" {{ request('studio_id') == $s->id ? 'selected' : '' }}>
                        {{ $s->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label text-xs">Tanggal</label>
            <input type="date" name="date" value="{{ request('date') }}" class="form-input">
        </div>
        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->hasAny(['movie_id', 'studio_id', 'date']))
            <a href="{{ route('admin.schedules.index') }}" class="btn-secondary">Reset</a>
        @endif
    </div>
</form>

<div class="bg-white rounded-2xl shadow-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="table-admin">
            <thead>
                <tr>
                    <th>Film</th>
                    <th>Studio</th>
                    <th>Tanggal</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Tipe</th>
                    <th>Harga Regular</th>
                    <th>Kursi Tersisa</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $s)
                    <tr>
                        <td class="font-medium max-w-[200px]">
                            <span class="line-clamp-1">{{ $s->movie->title ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="badge badge-primary">{{ $s->studio->name ?? '-' }}</span>
                        </td>
                        <td class="text-sm text-charcoal-muted">{{ $s->show_date->format('d/m/Y') }}</td>
                        <td class="font-mono text-sm font-bold text-primary">{{ substr($s->start_time, 0, 5) }}</td>
                        <td class="font-mono text-sm text-charcoal-muted">{{ substr($s->end_time, 0, 5) }}</td>
                        <td>
                            <span class="badge badge-blue text-xs">{{ $s->language_type_label }}</span>
                        </td>
                        <td class="text-sm">Rp {{ number_format($s->price_regular, 0, ',', '.') }}</td>
                        <td>
                            <span class="font-semibold {{ $s->available_seats_count < 10 ? 'text-red-600' : 'text-green-600' }}">
                                {{ $s->available_seats_count }}
                            </span>
                        </td>
                        <td>
                            @if($s->is_active)
                                <span class="badge badge-green">Aktif</span>
                            @else
                                <span class="badge badge-gray">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex gap-1.5">
                                <a href="{{ route('admin.schedules.edit', $s) }}" class="btn-secondary btn-sm">Edit</a>
                                <form action="{{ route('admin.schedules.destroy', $s) }}" method="POST"
                                      onsubmit="return confirm('Hapus jadwal ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm bg-red-50 text-red-600 hover:bg-red-600 hover:text-white">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-10 text-gray-400">
                            <div class="text-4xl mb-2">📅</div>
                            <p>Tidak ada jadwal. <a href="{{ route('admin.schedules.create') }}" class="text-primary font-semibold">Tambah sekarang</a></p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $schedules->links() }}</div>
</div>
@endsection
