@extends('layouts.admin')
@section('title', 'Kelola Film')

@section('content')
<div class="flex justify-between items-center mb-5">
    <div>
        <p class="text-sm text-gray-500">{{ $movies->total() }} film ditemukan</p>
    </div>
    <a href="{{ route('admin.movies.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Film
    </a>
</div>

{{-- Filters --}}
<form method="GET" class="bg-white rounded-2xl p-4 shadow-card mb-5">
    <div class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="form-label text-xs">Cari Film</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Judul film..." class="form-input max-w-xs">
        </div>
        <div>
            <label class="form-label text-xs">Status</label>
            <select name="status" class="form-input max-w-xs">
                <option value="">Semua Status</option>
                <option value="now_playing" {{ request('status') === 'now_playing' ? 'selected' : '' }}>Sedang Tayang</option>
                <option value="coming_soon" {{ request('status') === 'coming_soon' ? 'selected' : '' }}>Segera Tayang</option>
                <option value="ended" {{ request('status') === 'ended' ? 'selected' : '' }}>Selesai</option>
            </select>
        </div>
        <button type="submit" class="btn-primary">Cari</button>
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.movies.index') }}" class="btn-secondary">Reset</a>
        @endif
    </div>
</form>

<div class="bg-white rounded-2xl shadow-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="table-admin">
            <thead>
                <tr>
                    <th>Film</th>
                    <th>Genre</th>
                    <th>Durasi</th>
                    <th>Status</th>
                    <th>Rilis</th>
                    <th>Ulasan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movies as $movie)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-14 rounded-lg overflow-hidden bg-cream-dark flex-shrink-0">
                                    @if($movie->poster_image)
                                        <img src="{{ $movie->poster_url }}" class="w-full h-full object-cover" alt="">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-xl bg-cream">🎬</div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-charcoal truncate max-w-[200px]">{{ $movie->title }}</p>
                                    <p class="text-xs text-charcoal-muted">{{ $movie->rating }} · {{ $movie->language }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-sm text-charcoal-muted max-w-[150px]">
                            <div class="flex flex-wrap gap-1">
                                @foreach(array_slice($movie->genre ?? [], 0, 2) as $genre)
                                    <span class="badge badge-primary text-xs">{{ $genre }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="text-sm">{{ $movie->duration_formatted }}</td>
                        <td>
                            <span class="badge badge-{{ $movie->status_color }}">{{ $movie->status_label }}</span>
                        </td>
                        <td class="text-sm text-charcoal-muted">{{ $movie->release_date->format('d/m/Y') }}</td>
                        <td>
                            <span class="flex items-center gap-1 text-sm">
                                ⭐ {{ $movie->reviews_count }}
                            </span>
                        </td>
                        <td>
                            <div class="flex gap-1.5">
                                <a href="{{ route('admin.movies.show', $movie) }}"
                                   class="btn btn-sm bg-blue-50 text-blue-600 hover:bg-blue-100">
                                    Detail
                                </a>
                                <a href="{{ route('admin.movies.edit', $movie) }}"
                                   class="btn-secondary btn-sm">Edit</a>
                                <form action="{{ route('admin.movies.destroy', $movie) }}" method="POST"
                                      onsubmit="return confirm('Hapus film \'{{ addslashes($movie->title) }}\'? Tindakan ini tidak bisa dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm bg-red-50 text-red-600 hover:bg-red-600 hover:text-white">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-10 text-gray-400">
                            <div class="text-4xl mb-2">🎬</div>
                            <p>Belum ada film. <a href="{{ route('admin.movies.create') }}" class="text-primary font-semibold">Tambah sekarang</a></p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $movies->links() }}</div>
</div>
@endsection
